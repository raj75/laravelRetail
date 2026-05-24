@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Dashboard</h4>
    <div>
        <a href="{{ route('invoices.create', 'sale') }}" class="btn btn-vyapar btn-sm"><i class="bi bi-plus-lg"></i> Sale Invoice</a>
        <a href="{{ route('invoices.create', 'purchase') }}" class="btn btn-outline-secondary btn-sm">Purchase</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-stat p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Today's Sales</div>
                    <h4 class="mb-0">₹{{ number_format($todaySales, 2) }}</h4>
                </div>
                <div class="icon"><i class="bi bi-calendar-day fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-stat p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Month Sales</div>
                    <h4 class="mb-0">₹{{ number_format($monthSales, 2) }}</h4>
                </div>
                <div class="icon"><i class="bi bi-graph-up-arrow fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-stat p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Receivable</div>
                    <h4 class="mb-0 text-success">₹{{ number_format($receivable, 2) }}</h4>
                </div>
                <div class="icon"><i class="bi bi-arrow-down-left fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-stat p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Payable</div>
                    <h4 class="mb-0 text-danger">₹{{ number_format($payable, 2) }}</h4>
                </div>
                <div class="icon"><i class="bi bi-arrow-up-right fs-4"></i></div>
            </div>
        </div>
    </div>
</div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="table-card p-3">
            <h6 class="mb-3">Recent Transactions</h6>
            <table class="table table-sm table-hover mb-0">
                <thead><tr><th>#</th><th>Party</th><th>Type</th><th>Date</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                @forelse($recentInvoices as $inv)
                    <tr>
                        <td><a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                        <td>{{ $inv->party?->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $inv->typeLabel() }}</span></td>
                        <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                        <td class="text-end">₹{{ number_format($inv->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted text-center">No transactions yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-3 mb-3">
            <h6 class="mb-3 text-danger"><i class="bi bi-exclamation-triangle"></i> Low Stock</h6>
            @forelse($lowStockItems as $item)
                <div class="d-flex justify-content-between small py-1 border-bottom">
                    <span>{{ $item->name }}</span>
                    <strong>{{ $item->stock_qty }} left</strong>
                </div>
            @empty
                <p class="text-muted small mb-0">All items stocked well</p>
            @endforelse
            <a href="{{ route('reports.low-stock') }}" class="btn btn-link btn-sm p-0 mt-2">View all</a>
        </div>
        <div class="card card-stat p-3">
            <div class="text-muted small">Month Purchases</div>
            <h5>₹{{ number_format($monthPurchases, 2) }}</h5>
        </div>
    </div>
</div>
@endsection
