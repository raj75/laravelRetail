@extends('layouts.app')
@section('title', 'Stock Report')
@section('content')
<h4>Stock Report</h4>
<p>Total inventory value (at purchase price): <strong>₹{{ number_format($stockValue, 2) }}</strong></p>
<div class="table-card"><table class="table mb-0"><thead class="table-light"><tr><th>Item</th><th>Qty</th><th>Rate</th><th class="text-end">Value</th></tr></thead>
<tbody>@foreach($items as $i)<tr><td>{{ $i->name }}</td><td>{{ $i->stock_qty }}</td><td>₹{{ number_format($i->purchase_price,2) }}</td><td class="text-end">₹{{ number_format($i->stock_qty * $i->purchase_price, 2) }}</td></tr>@endforeach</tbody></table></div>
@endsection
