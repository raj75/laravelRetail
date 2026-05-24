@extends('layouts.app')
@section('title', 'Purchase Report')
@section('content')
<h4>Purchase Report</h4>
<form class="row g-2 my-3"><div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm"></div><div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm"></div><div class="col-auto"><button class="btn btn-secondary btn-sm">Filter</button></div></form>
<p><strong>Total: ₹{{ number_format($total, 2) }}</strong></p>
<div class="table-card"><table class="table mb-0"><thead class="table-light"><tr><th>Bill</th><th>Supplier</th><th>Date</th><th class="text-end">Amount</th></tr></thead>
<tbody>@foreach($invoices as $i)<tr><td>{{ $i->invoice_number }}</td><td>{{ $i->party?->name }}</td><td>{{ $i->invoice_date->format('d/m/Y') }}</td><td class="text-end">₹{{ number_format($i->total_amount,2) }}</td></tr>@endforeach</tbody></table></div>
@endsection
