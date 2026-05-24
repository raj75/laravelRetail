@extends('layouts.app')
@section('title', $party->name)
@section('content')
<div class="d-flex justify-content-between mb-3">
    <div>
        <h4>{{ $party->name }}</h4>
        <span class="badge bg-info">{{ ucfirst($party->type) }}</span>
        <span class="ms-2">Balance: <strong>₹{{ number_format($party->current_balance, 2) }}</strong></span>
    </div>
    <a href="{{ route('parties.edit', $party) }}" class="btn btn-outline-primary btn-sm">Edit</a>
</div>
<div class="row g-3">
    <div class="col-md-6 table-card p-3">
        <h6>Recent Invoices</h6>
        <table class="table table-sm"><thead><tr><th>#</th><th>Date</th><th>Amount</th></tr></thead>
        <tbody>@foreach($party->invoices as $i)<tr><td><a href="{{ route('invoices.show', $i) }}">{{ $i->invoice_number }}</a></td><td>{{ $i->invoice_date->format('d/m/Y') }}</td><td>₹{{ number_format($i->total_amount,2) }}</td></tr>@endforeach</tbody></table>
    </div>
    <div class="col-md-6 table-card p-3">
        <h6>Recent Payments</h6>
        <table class="table table-sm"><thead><tr><th>#</th><th>Date</th><th>Amount</th></tr></thead>
        <tbody>@foreach($party->payments as $p)<tr><td>{{ $p->payment_number }}</td><td>{{ $p->payment_date->format('d/m/Y') }}</td><td>₹{{ number_format($p->amount,2) }}</td></tr>@endforeach</tbody></table>
    </div>
</div>
@endsection
