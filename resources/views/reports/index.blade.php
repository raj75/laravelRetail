@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h4 class="mb-4">Reports</h4>
<div class="row g-3">
    @foreach([
        ['Sales Report', 'reports.sales', 'bi-receipt'],
        ['Purchase Report', 'reports.purchases', 'bi-bag'],
        ['Stock Report', 'reports.stock', 'bi-boxes'],
        ['Party Ledger', 'reports.party-ledger', 'bi-journal'],
        ['Profit & Loss', 'reports.profit-loss', 'bi-graph-up'],
        ['GST Summary', 'reports.gst', 'bi-percent'],
        ['Low Stock', 'reports.low-stock', 'bi-exclamation-triangle'],
    ] as [$title, $route, $icon])
    <div class="col-md-4"><a href="{{ route($route) }}" class="card card-stat p-3 text-decoration-none text-dark d-block"><i class="bi {{ $icon }} fs-3 text-success"></i><h6 class="mt-2 mb-0">{{ $title }}</h6></a></div>
    @endforeach
</div>
@endsection
