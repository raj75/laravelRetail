@extends('layouts.app')
@section('title', 'Low Stock')
@section('content')
<h4>Low Stock Items</h4>
<div class="table-card mt-3"><table class="table mb-0"><thead class="table-light"><tr><th>Item</th><th>Current</th><th>Alert Level</th></tr></thead>
<tbody>@forelse($items as $i)<tr><td>{{ $i->name }}</td><td class="text-danger">{{ $i->stock_qty }}</td><td>{{ $i->low_stock_alert }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No low stock items</td></tr>@endforelse</tbody></table></div>
@endsection
