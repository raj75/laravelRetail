<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — {{ $business->business_name ?? 'LaravelRetail' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --vyapar: #0d7a6f; --vyapar-dark: #065a52; --vyapar-light: #e8f5f3; }
        body { background: #f4f6f8; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar { width: 260px; min-height: 100vh; background: linear-gradient(180deg, var(--vyapar) 0%, var(--vyapar-dark) 100%); color: #fff; position: fixed; left: 0; top: 0; z-index: 1000; overflow-y: auto; }
        .sidebar .brand { padding: 1.25rem; font-weight: 700; font-size: 1.25rem; border-bottom: 1px solid rgba(255,255,255,.15); }
        .sidebar a { color: rgba(255,255,255,.9); text-decoration: none; padding: .6rem 1.25rem; display: flex; align-items: center; gap: .6rem; font-size: .9rem; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,.12); color: #fff; }
        .sidebar .nav-section { font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; opacity: .6; padding: 1rem 1.25rem .35rem; }
        .main-content { margin-left: 260px; padding: 1.5rem; }
        .card-stat { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .card-stat .icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: var(--vyapar-light); color: var(--vyapar); }
        .btn-vyapar { background: var(--vyapar); border-color: var(--vyapar); color: #fff; }
        .btn-vyapar:hover { background: var(--vyapar-dark); border-color: var(--vyapar-dark); color: #fff; }
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06); overflow: hidden; }
        @media (max-width: 768px) { .sidebar { width: 100%; position: relative; min-height: auto; } .main-content { margin-left: 0; } }
    </style>
    @stack('styles')
</head>
<body>
@if(auth()->check())
<aside class="sidebar">
    <div class="brand"><i class="bi bi-shop"></i> LaravelRetail</div>
    <nav>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

        <div class="nav-section">Sales</div>
        <a href="{{ route('invoices.create', 'sale') }}"><i class="bi bi-receipt"></i> New Sale</a>
        <a href="{{ route('invoices.index', ['type' => 'sale']) }}"><i class="bi bi-list-ul"></i> Sale Invoices</a>
        <a href="{{ route('invoices.create', 'estimate') }}"><i class="bi bi-file-earmark-text"></i> Estimate</a>
        <a href="{{ route('invoices.create', 'delivery_challan') }}"><i class="bi bi-truck"></i> Delivery Challan</a>
        <a href="{{ route('invoices.create', 'credit_note') }}"><i class="bi bi-arrow-return-left"></i> Credit Note</a>

        <div class="nav-section">Purchase</div>
        <a href="{{ route('invoices.create', 'purchase') }}"><i class="bi bi-bag-plus"></i> New Purchase</a>
        <a href="{{ route('invoices.index', ['type' => 'purchase']) }}"><i class="bi bi-list-check"></i> Purchase Bills</a>
        <a href="{{ route('invoices.create', 'debit_note') }}"><i class="bi bi-arrow-return-right"></i> Debit Note</a>

        <div class="nav-section">Parties & Items</div>
        <a href="{{ route('parties.index') }}"><i class="bi bi-people"></i> Parties</a>
        <a href="{{ route('items.index') }}"><i class="bi bi-box-seam"></i> Items / Inventory</a>
        <a href="{{ route('stock.index') }}"><i class="bi bi-boxes"></i> Stock</a>

        <div class="nav-section">Money</div>
        <a href="{{ route('payments.index') }}"><i class="bi bi-cash-coin"></i> Payments</a>
        <a href="{{ route('expenses.index') }}"><i class="bi bi-wallet2"></i> Expenses</a>
        <a href="{{ route('accounts.index') }}"><i class="bi bi-bank"></i> Cash & Bank</a>

        <div class="nav-section">Reports</div>
        <a href="{{ route('reports.index') }}"><i class="bi bi-graph-up"></i> All Reports</a>

        <div class="nav-section">Setup</div>
        <a href="{{ route('settings.edit') }}"><i class="bi bi-gear"></i> Business Settings</a>
        <form action="{{ route('logout') }}" method="POST" class="mt-3 px-3">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </nav>
</aside>
<main class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
@else
    @yield('content')
@endif
@if(auth()->check())
    @include('components.barcode-scanner')
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
