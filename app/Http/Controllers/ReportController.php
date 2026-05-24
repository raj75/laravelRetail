<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Party;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $invoices = Invoice::with('party')
            ->where('type', 'sale')
            ->where('status', 'final')
            ->whereBetween('invoice_date', [$from, $to])
            ->orderBy('invoice_date')
            ->get();

        $total = $invoices->sum('total_amount');

        return view('reports.sales', compact('invoices', 'total', 'from', 'to'));
    }

    public function purchases(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $invoices = Invoice::with('party')
            ->where('type', 'purchase')
            ->where('status', 'final')
            ->whereBetween('invoice_date', [$from, $to])
            ->orderBy('invoice_date')
            ->get();

        $total = $invoices->sum('total_amount');

        return view('reports.purchases', compact('invoices', 'total', 'from', 'to'));
    }

    public function stock()
    {
        $items = Item::with(['category', 'unit'])->orderBy('name')->get();
        $stockValue = $items->sum(fn ($i) => (float) $i->stock_qty * (float) $i->purchase_price);

        return view('reports.stock', compact('items', 'stockValue'));
    }

    public function partyLedger(Request $request)
    {
        $party = null;
        $invoices = collect();
        $payments = collect();

        if ($request->filled('party_id')) {
            $party = Party::findOrFail($request->party_id);
            $invoices = $party->invoices()->orderBy('invoice_date')->get();
            $payments = $party->payments()->orderBy('payment_date')->get();
        }

        $parties = Party::orderBy('name')->get();

        return view('reports.party-ledger', compact('party', 'invoices', 'payments', 'parties'));
    }

    public function profitLoss(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $sales = Invoice::where('type', 'sale')->where('status', 'final')
            ->whereBetween('invoice_date', [$from, $to])->sum('total_amount');
        $purchases = Invoice::where('type', 'purchase')->where('status', 'final')
            ->whereBetween('invoice_date', [$from, $to])->sum('total_amount');
        $expenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $profit = $sales - $purchases - $expenses;

        return view('reports.profit-loss', compact('sales', 'purchases', 'expenses', 'profit', 'from', 'to'));
    }

    public function gst(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $salesGst = Invoice::where('type', 'sale')->where('status', 'final')
            ->whereBetween('invoice_date', [$from, $to])
            ->select(
                DB::raw('SUM(cgst_amount) as cgst'),
                DB::raw('SUM(sgst_amount) as sgst'),
                DB::raw('SUM(igst_amount) as igst'),
                DB::raw('SUM(taxable_amount) as taxable')
            )->first();

        $purchaseGst = Invoice::where('type', 'purchase')->where('status', 'final')
            ->whereBetween('invoice_date', [$from, $to])
            ->select(
                DB::raw('SUM(cgst_amount) as cgst'),
                DB::raw('SUM(sgst_amount) as sgst'),
                DB::raw('SUM(igst_amount) as igst'),
                DB::raw('SUM(taxable_amount) as taxable')
            )->first();

        return view('reports.gst', compact('salesGst', 'purchaseGst', 'from', 'to'));
    }

    public function lowStock()
    {
        $items = Item::where('track_inventory', true)
            ->whereColumn('stock_qty', '<=', 'low_stock_alert')
            ->orderBy('stock_qty')
            ->get();

        return view('reports.low-stock', compact('items'));
    }
}
