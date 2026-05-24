@extends('layouts.app')
@section('title', 'Parties')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Parties (Customers & Suppliers)</h4>
    <a href="{{ route('parties.create') }}" class="btn btn-vyapar btn-sm"><i class="bi bi-plus"></i> Add Party</a>
</div>
<form class="row g-2 mb-3">
    <div class="col-md-4"><input name="search" class="form-control form-control-sm" placeholder="Search name, phone, GSTIN" value="{{ request('search') }}"></div>
    <div class="col-md-3">
        <select name="type" class="form-select form-select-sm">
            <option value="">All types</option>
            <option value="customer" @selected(request('type')=='customer')>Customer</option>
            <option value="supplier" @selected(request('type')=='supplier')>Supplier</option>
            <option value="both" @selected(request('type')=='both')>Both</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-secondary btn-sm">Filter</button></div>
</form>
<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Type</th><th>Phone</th><th>GSTIN</th><th class="text-end">Balance</th><th></th></tr></thead>
        <tbody>
        @foreach($parties as $party)
            <tr>
                <td><a href="{{ route('parties.show', $party) }}">{{ $party->name }}</a></td>
                <td><span class="badge bg-info">{{ ucfirst($party->type) }}</span></td>
                <td>{{ $party->phone ?? '—' }}</td>
                <td>{{ $party->gstin ?? '—' }}</td>
                <td class="text-end">₹{{ number_format($party->current_balance, 2) }}</td>
                <td class="text-end">
                    <a href="{{ route('parties.edit', $party) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="p-2">{{ $parties->links() }}</div>
</div>
@endsection
