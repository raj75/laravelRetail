<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Party;
use App\Models\Payment;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['party', 'account', 'invoice'])
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->latest('payment_date')
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        return view('payments.form', [
            'payment' => new Payment(['payment_date' => now()->toDateString()]),
            'parties' => Party::orderBy('name')->get(),
            'accounts' => Account::orderBy('name')->get(),
            'invoices' => Invoice::where('balance_amount', '>', 0)->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request, AccountService $accountService)
    {
        $data = $request->validate([
            'type' => ['required', 'in:receive,pay'],
            'party_id' => ['required', 'exists:parties,id'],
            'account_id' => ['required', 'exists:accounts,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_mode' => ['nullable', 'string'],
            'reference_no' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $accountService) {
            $prefix = $data['type'] === 'receive' ? 'RCV' : 'PAY';
            $num = $prefix.'-'.str_pad((string) (Payment::count() + 1), 5, '0', STR_PAD_LEFT);

            $payment = Payment::create([
                ...$data,
                'payment_number' => $num,
                'user_id' => Auth::id(),
            ]);

            $account = Account::find($data['account_id']);
            if ($data['type'] === 'receive') {
                $accountService->credit($account, (float) $data['amount'], Payment::class, $payment->id);
            } else {
                $accountService->debit($account, (float) $data['amount'], Payment::class, $payment->id);
            }

            $party = Party::find($data['party_id']);
            if ($data['type'] === 'receive') {
                $party->current_balance = max(0, (float) $party->current_balance - (float) $data['amount']);
            } else {
                $party->current_balance = max(0, (float) $party->current_balance - (float) $data['amount']);
            }
            $party->save();

            if (! empty($data['invoice_id'])) {
                $invoice = Invoice::find($data['invoice_id']);
                $invoice->paid_amount += (float) $data['amount'];
                $invoice->balance_amount = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
                $invoice->payment_status = $invoice->balance_amount <= 0 ? 'paid' : 'partial';
                $invoice->save();
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
