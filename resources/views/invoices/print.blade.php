<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; max-width: 800px; margin: 20px auto; }
        .header { border-bottom: 2px solid #0d7a6f; padding-bottom: 10px; margin-bottom: 15px; }
        .brand { color: #0d7a6f; font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
@php $business = $business ?? \App\Models\BusinessSetting::current(); @endphp
<button class="no-print" onclick="window.print()">Print</button>
<div class="header">
    <div class="brand">{{ $business->business_name }}</div>
    <div>{{ $business->address }} {{ $business->city }} {{ $business->state }} {{ $business->pincode }}</div>
    <div>GSTIN: {{ $business->gstin ?? '—' }} | Phone: {{ $business->phone ?? '—' }}</div>
</div>
<h3>{{ $invoice->typeLabel() }}</h3>
<p><strong>Invoice #:</strong> {{ $invoice->invoice_number }} &nbsp; <strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
<p><strong>Party:</strong> {{ $invoice->party?->name ?? 'Walk-in Customer' }}<br>
@if($invoice->party?->gstin) GSTIN: {{ $invoice->party->gstin }}<br>@endif
{{ $invoice->party?->billing_address }}</p>
<table>
    <thead><tr><th>#</th><th>Description</th><th>HSN</th><th>Qty</th><th>Rate</th><th>GST%</th><th class="text-right">Amount</th></tr></thead>
    <tbody>
    @foreach($invoice->items as $i => $line)
    <tr><td>{{ $i+1 }}</td><td>{{ $line->description }}</td><td>{{ $line->hsn_code }}</td><td>{{ $line->quantity }}</td><td>{{ number_format($line->rate,2) }}</td><td>{{ $line->gst_rate }}%</td><td class="text-right">{{ number_format($line->amount,2) }}</td></tr>
    @endforeach
    </tbody>
</table>
<table style="margin-top:15px; width: 300px; margin-left: auto;">
    <tr><td>Taxable</td><td class="text-right">₹{{ number_format($invoice->taxable_amount,2) }}</td></tr>
    <tr><td>CGST</td><td class="text-right">₹{{ number_format($invoice->cgst_amount,2) }}</td></tr>
    <tr><td>SGST</td><td class="text-right">₹{{ number_format($invoice->sgst_amount,2) }}</td></tr>
    <tr><td>IGST</td><td class="text-right">₹{{ number_format($invoice->igst_amount,2) }}</td></tr>
    <tr><td><strong>Total</strong></td><td class="text-right"><strong>₹{{ number_format($invoice->total_amount,2) }}</strong></td></tr>
</table>
@if($business->terms_conditions)<p style="margin-top:20px;font-size:10px;"><strong>Terms:</strong> {{ $business->terms_conditions }}</p>@endif
</body>
</html>
