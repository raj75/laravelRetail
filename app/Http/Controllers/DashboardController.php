<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Party;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $todaySales = Invoice::where('type', 'sale')
            ->where('status', 'final')
            ->whereDate('invoice_date', $today)
            ->sum('total_amount');

        $monthSales = Invoice::where('type', 'sale')
            ->where('status', 'final')
            ->where('invoice_date', '>=', $monthStart)
            ->sum('total_amount');

        $monthPurchases = Invoice::where('type', 'purchase')
            ->where('status', 'final')
            ->where('invoice_date', '>=', $monthStart)
            ->sum('total_amount');

        $receivable = Party::customers()->sum('current_balance');
        $payable = Party::suppliers()->sum('current_balance');

        $lowStockItems = Item::where('track_inventory', true)
            ->whereColumn('stock_qty', '<=', 'low_stock_alert')
            ->orderBy('stock_qty')
            ->limit(10)
            ->get();

        $recentInvoices = Invoice::with('party')
            ->whereIn('type', ['sale', 'purchase'])
            ->latest('invoice_date')
            ->limit(8)
            ->get();

        $salesChart = Invoice::where('type', 'sale')
            ->where('status', 'final')
            ->where('invoice_date', '>=', Carbon::now()->subDays(6))
            ->select(DB::raw('DATE(invoice_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        return view('dashboard', compact(
            'todaySales', 'monthSales', 'monthPurchases',
            'receivable', 'payable', 'lowStockItems', 'recentInvoices', 'salesChart'
        ));
    }
}
