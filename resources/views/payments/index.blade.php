@extends('layouts.app')
@section('title', 'Payments')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Payment In / Out</h4>
    <a href="{{ route('payments.create') }}" class="btn btn-vyapar btn-sm">Record Payment</a>
</div>
<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>#</th><th>Type</th><th>Party</th><th>Date</th><th>Mode</th><th class="text-end">Amount</th></tr></thead>
        <tbody>@foreach($payments as $p)<tr><td>{{ $p->payment_number }}</td><td><span class="badge bg-{{ $p->type=='receive'?'success':'danger' }}">{{ ucfirst($p->type) }}</span></td><td>{{ $p->party->name }}</td><td>{{ $p->payment_date->format('d/m/Y') }}</td><td>{{ $p->payment_mode }}</td><td class="text-end">₹{{ number_format($p->amount,2) }}</td></tr>@endforeach</tbody>
    </table>
    <div class="p-2">{{ $payments->links() }}</div>
</div>
@endsection
