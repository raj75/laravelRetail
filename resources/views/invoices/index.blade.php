@extends('layouts.app')
@section('title', \App\Models\Invoice::TYPES[$type] ?? 'Invoices')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>{{ \App\Models\Invoice::TYPES[$type] ?? 'Invoices' }}</h4>
    <a href="{{ route('invoices.create', $type) }}" class="btn btn-vyapar btn-sm"><i class="bi bi-plus"></i> Create New</a>
</div>
<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Invoice #</th><th>Party</th><th>Date</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Balance</th><th></th></tr></thead>
        <tbody>
        @foreach($invoices as $inv)
            <tr>
                <td><a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                <td>{{ $inv->party?->name ?? '—' }}</td>
                <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                <td><span class="badge bg-{{ $inv->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $inv->payment_status }}</span></td>
                <td class="text-end">₹{{ number_format($inv->total_amount, 2) }}</td>
                <td class="text-end">₹{{ number_format($inv->balance_amount, 2) }}</td>
                <td class="text-end">
                    <a href="{{ route('invoices.print', $inv) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-printer"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="p-2">{{ $invoices->links() }}</div>
</div>
@endsection
