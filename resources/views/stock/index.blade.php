@extends('layouts.app')
@section('title', 'Stock')
@section('content')
<h4 class="mb-3">Stock Management</h4>
<div class="table-card p-3 mb-3">
    <h6>Manual Stock Adjustment</h6>
    <form method="POST" action="{{ route('stock.adjust') }}" class="row g-2 align-items-end">@csrf
        <div class="col-md-4"><label>Item</label><select name="item_id" class="form-select" required>@foreach($items as $i)<option value="{{ $i->id }}">{{ $i->name }} ({{ $i->stock_qty }})</option>@endforeach</select></div>
        <div class="col-md-2"><label>Type</label><select name="type" class="form-select"><option value="in">Stock In</option><option value="out">Stock Out</option></select></div>
        <div class="col-md-2"><label>Qty</label><input name="quantity" type="number" step="0.001" class="form-control" required></div>
        <div class="col-md-3"><label>Notes</label><input name="notes" class="form-control"></div>
        <div class="col-auto"><button class="btn btn-vyapar">Adjust</button></div>
    </form>
</div>
<div class="table-card"><table class="table mb-0"><thead class="table-light"><tr><th>Item</th><th>Stock</th><th>Alert At</th></tr></thead>
<tbody>@foreach($items as $i)<tr class="{{ $i->isLowStock()?'table-warning':'' }}"><td>{{ $i->name }}</td><td>{{ $i->stock_qty }}</td><td>{{ $i->low_stock_alert }}</td></tr>@endforeach</tbody></table>
<div class="p-2">{{ $items->links() }}</div></div>
@endsection
