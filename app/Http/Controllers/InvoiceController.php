<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Party;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        $type = $request->get('type', 'sale');
        $invoices = Invoice::with('party')
            ->where('type', $type)
            ->when($request->filled('from'), fn ($q) => $q->whereDate('invoice_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('invoice_date', '<=', $request->to))
            ->latest('invoice_date')
            ->paginate(20)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'type'));
    }

    public function create(string $type)
    {
        abort_unless(array_key_exists($type, Invoice::TYPES), 404);

        $partyQuery = in_array($type, ['purchase', 'purchase_order', 'credit_note'], true)
            ? Party::suppliers()
            : Party::customers();

        return view('invoices.form', [
            'type' => $type,
            'invoice' => new Invoice(['type' => $type, 'invoice_date' => now()->toDateString()]),
            'parties' => $partyQuery->where('is_active', true)->orderBy('name')->get(),
            'accounts' => Account::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, Invoice::TYPES), 404);

        $data = $this->validated($request);
        $invoice = $this->invoiceService->store($type, $data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Saved successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['party', 'items.item', 'account']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

        $partyQuery = in_array($invoice->type, ['purchase', 'purchase_order', 'credit_note'], true)
            ? Party::suppliers()
            : Party::customers();

        return view('invoices.form', [
            'type' => $invoice->type,
            'invoice' => $invoice,
            'parties' => $partyQuery->orderBy('name')->get(),
            'accounts' => Account::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $this->validated($request);
        $this->invoiceService->update($invoice, $data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'final') {
            app(\App\Services\StockService::class)->reverseInvoice($invoice);
        }
        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index', ['type' => $invoice->type])->with('success', 'Deleted.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['party', 'items.item']);

        return view('invoices.print', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['party', 'items.item']);
        $business = \App\Models\BusinessSetting::current();
        $pdf = Pdf::loadView('invoices.print', compact('invoice', 'business'));

        return $pdf->download($invoice->invoice_number.'.pdf');
    }

    public function convert(Request $request, Invoice $invoice)
    {
        $request->validate(['to_type' => ['required', 'in:sale,purchase']]);
        $new = $this->invoiceService->convert($invoice, $request->to_type);

        return redirect()->route('invoices.show', $new)->with('success', 'Converted to '.$new->typeLabel());
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'party_id' => ['nullable', 'exists:parties,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,final,cancelled'],
            'payment_mode' => ['nullable', 'string'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'in:amount,percent'],
            'is_inter_state' => ['nullable', 'boolean'],
            'place_of_supply' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['nullable', 'exists:items,id'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.hsn_code' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.gst_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
    }
}
