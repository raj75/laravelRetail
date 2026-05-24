@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('content')
<div class="d-flex justify-content-between mb-3">
    <div>
        <h4>{{ $invoice->typeLabel() }} — {{ $invoice->invoice_number }}</h4>
        <span class="text-muted">{{ $invoice->party?->name ?? 'Walk-in' }} · {{ $invoice->invoice_date->format('d M Y') }}</span>
    </div>
    <div class="btn-group">
        <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-printer"></i> Print</a>
        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        @if($invoice->type === 'estimate')
        <form method="POST" action="{{ route('invoices.convert', $invoice) }}" class="d-inline">@csrf<input type="hidden" name="to_type" value="sale"><button class="btn btn-vyapar btn-sm">Convert to Sale</button></form>
        @endif
    </div>
</div>
<div class="table-card p-4">
    <table class="table"><thead><tr><th>Item</th><th>HSN</th><th>Qty</th><th>Rate</th><th class="text-end">Amount</th></tr></thead>
    <tbody>@foreach($invoice->items as $line)<tr><td>{{ $line->description }}</td><td>{{ $line->hsn_code }}</td><td>{{ $line->quantity }}</td><td>₹{{ number_format($line->rate,2) }}</td><td class="text-end">₹{{ number_format($line->amount,2) }}</td></tr>@endforeach</tbody>
    <tfoot class="table-light">
        <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">₹{{ number_format($invoice->subtotal,2) }}</td></tr>
        <tr><td colspan="4" class="text-end">CGST</td><td class="text-end">₹{{ number_format($invoice->cgst_amount,2) }}</td></tr>
        <tr><td colspan="4" class="text-end">SGST</td><td class="text-end">₹{{ number_format($invoice->sgst_amount,2) }}</td></tr>
        <tr><td colspan="4" class="text-end">IGST</td><td class="text-end">₹{{ number_format($invoice->igst_amount,2) }}</td></tr>
        <tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold">₹{{ number_format($invoice->total_amount,2) }}</td></tr>
        <tr><td colspan="4" class="text-end">Paid / Balance</td><td class="text-end">₹{{ number_format($invoice->paid_amount,2) }} / ₹{{ number_format($invoice->balance_amount,2) }}</td></tr>
    </tfoot>
    </table>
</div>
@endsection
