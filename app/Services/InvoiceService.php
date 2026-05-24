<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceSequence;
use App\Models\Party;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        protected StockService $stockService,
        protected AccountService $accountService,
    ) {}

    public function store(string $type, array $data): Invoice
    {
        return DB::transaction(function () use ($type, $data) {
            $isInterState = (bool) ($data['is_inter_state'] ?? false);
            $lines = $this->buildLines($data['items'] ?? [], $isInterState);
            $totals = GstCalculator::summarizeTotals($lines, (float) ($data['discount_amount'] ?? 0));

            $paid = (float) ($data['paid_amount'] ?? 0);
            $total = $totals['total_amount'];
            $balance = max(0, $total - $paid);

            $invoice = Invoice::create([
                'type' => $type,
                'invoice_number' => InvoiceSequence::nextNumber($type),
                'party_id' => $data['party_id'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'] ?? 'final',
                'payment_status' => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'payment_mode' => $data['payment_mode'] ?? null,
                'account_id' => $data['account_id'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => (float) ($data['discount_amount'] ?? 0),
                'discount_type' => $data['discount_type'] ?? 'amount',
                'taxable_amount' => $totals['taxable_amount'],
                'cgst_amount' => $totals['cgst_amount'],
                'sgst_amount' => $totals['sgst_amount'],
                'igst_amount' => $totals['igst_amount'],
                'round_off' => $totals['round_off'],
                'total_amount' => $total,
                'paid_amount' => $paid,
                'balance_amount' => $balance,
                'place_of_supply' => $data['place_of_supply'] ?? null,
                'is_inter_state' => $isInterState,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'user_id' => Auth::id(),
            ]);

            foreach ($lines as $line) {
                $invoice->items()->create($line);
            }

            if ($invoice->status === 'final') {
                $this->stockService->applyInvoice($invoice);
                $this->updatePartyBalance($invoice);
                $this->recordPaymentIfAny($invoice, $data);
            }

            return $invoice->load(['party', 'items.item']);
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            if ($invoice->status === 'final') {
                $this->stockService->reverseInvoice($invoice);
                $this->reversePartyBalance($invoice);
            }

            $invoice->items()->delete();

            $isInterState = (bool) ($data['is_inter_state'] ?? $invoice->is_inter_state);
            $lines = $this->buildLines($data['items'] ?? [], $isInterState);
            $totals = GstCalculator::summarizeTotals($lines, (float) ($data['discount_amount'] ?? 0));

            $paid = (float) ($data['paid_amount'] ?? $invoice->paid_amount);
            $total = $totals['total_amount'];
            $balance = max(0, $total - $paid);

            $invoice->update([
                'party_id' => $data['party_id'] ?? $invoice->party_id,
                'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'status' => $data['status'] ?? $invoice->status,
                'payment_status' => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'payment_mode' => $data['payment_mode'] ?? $invoice->payment_mode,
                'account_id' => $data['account_id'] ?? $invoice->account_id,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => (float) ($data['discount_amount'] ?? 0),
                'taxable_amount' => $totals['taxable_amount'],
                'cgst_amount' => $totals['cgst_amount'],
                'sgst_amount' => $totals['sgst_amount'],
                'igst_amount' => $totals['igst_amount'],
                'round_off' => $totals['round_off'],
                'total_amount' => $total,
                'paid_amount' => $paid,
                'balance_amount' => $balance,
                'is_inter_state' => $isInterState,
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);

            foreach ($lines as $line) {
                $invoice->items()->create($line);
            }

            if ($invoice->status === 'final') {
                $this->stockService->applyInvoice($invoice->fresh(['items']));
                $this->updatePartyBalance($invoice->fresh());
            }

            return $invoice->fresh(['party', 'items.item']);
        });
    }

    public function convert(Invoice $from, string $toType): Invoice
    {
        $data = [
            'party_id' => $from->party_id,
            'invoice_date' => now()->toDateString(),
            'is_inter_state' => $from->is_inter_state,
            'place_of_supply' => $from->place_of_supply,
            'notes' => 'Converted from '.$from->invoice_number,
            'items' => $from->items->map(fn ($i) => [
                'item_id' => $i->item_id,
                'description' => $i->description,
                'hsn_code' => $i->hsn_code,
                'quantity' => $i->quantity,
                'rate' => $i->rate,
                'discount' => $i->discount,
                'gst_rate' => $i->gst_rate,
            ])->toArray(),
        ];

        $invoice = $this->store($toType, $data);
        $invoice->update(['converted_from_id' => $from->id]);

        return $invoice;
    }

    protected function buildLines(array $items, bool $isInterState): array
    {
        $lines = [];

        foreach ($items as $row) {
            if (empty($row['description']) && empty($row['item_id'])) {
                continue;
            }

            $calc = GstCalculator::calculateLine(
                (float) ($row['quantity'] ?? 1),
                (float) ($row['rate'] ?? 0),
                (float) ($row['discount'] ?? 0),
                (float) ($row['gst_rate'] ?? 0),
                $isInterState
            );

            $lines[] = [
                'item_id' => $row['item_id'] ?? null,
                'description' => $row['description'] ?? 'Item',
                'hsn_code' => $row['hsn_code'] ?? null,
                'quantity' => (float) ($row['quantity'] ?? 1),
                'rate' => (float) ($row['rate'] ?? 0),
                'discount' => (float) ($row['discount'] ?? 0),
                'gst_rate' => (float) ($row['gst_rate'] ?? 0),
                'taxable_amount' => $calc['taxable'],
                'cgst' => $calc['cgst'],
                'sgst' => $calc['sgst'],
                'igst' => $calc['igst'],
                'amount' => $calc['amount'],
            ];
        }

        return $lines;
    }

    protected function updatePartyBalance(Invoice $invoice): void
    {
        if (! $invoice->party_id) {
            return;
        }

        $party = Party::find($invoice->party_id);
        if (! $party) {
            return;
        }

        $amount = (float) $invoice->balance_amount;

        if (in_array($invoice->type, ['sale', 'debit_note'], true)) {
            $party->current_balance += $amount;
        } elseif (in_array($invoice->type, ['purchase', 'credit_note'], true)) {
            $party->current_balance += $amount;
        }

        $party->save();
    }

    protected function reversePartyBalance(Invoice $invoice): void
    {
        if (! $invoice->party_id) {
            return;
        }

        $party = Party::find($invoice->party_id);
        if (! $party) {
            return;
        }

        $amount = (float) $invoice->balance_amount;

        if (in_array($invoice->type, ['sale', 'debit_note', 'purchase', 'credit_note'], true)) {
            $party->current_balance -= $amount;
            $party->save();
        }
    }

    protected function recordPaymentIfAny(Invoice $invoice, array $data): void
    {
        $paid = (float) ($data['paid_amount'] ?? 0);
        if ($paid <= 0 || empty($data['account_id']) || ! $invoice->party_id) {
            return;
        }

        $type = in_array($invoice->type, ['sale', 'debit_note'], true) ? 'receive' : 'pay';

        Payment::create([
            'payment_number' => InvoiceSequence::nextNumber('payment_'.$type),
            'type' => $type,
            'party_id' => $invoice->party_id,
            'account_id' => $data['account_id'],
            'invoice_id' => $invoice->id,
            'payment_date' => $invoice->invoice_date,
            'amount' => $paid,
            'payment_mode' => $data['payment_mode'] ?? 'cash',
            'user_id' => Auth::id(),
        ]);

        $account = $invoice->account;
        if ($account) {
            if ($type === 'receive') {
                $this->accountService->credit($account, $paid, Invoice::class, $invoice->id);
            } else {
                $this->accountService->debit($account, $paid, Invoice::class, $invoice->id);
            }
        }
    }
}
