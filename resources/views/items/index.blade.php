@extends('layouts.app')
@section('title', 'Items')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Items & Inventory</h4>
    <a href="{{ route('items.create') }}" class="btn btn-vyapar btn-sm"><i class="bi bi-plus"></i> Add Item</a>
</div>
<form class="mb-3 d-flex flex-wrap gap-2 align-items-center" id="itemsSearchForm">
    <input name="search" id="itemsSearchInput" class="form-control form-control-sm" style="max-width:280px" placeholder="Name, SKU, or barcode" value="{{ request('search') }}" autocomplete="off">
    <button type="button" class="btn btn-outline-success btn-sm" id="btnScanItemsList" title="Scan barcode to search"><i class="bi bi-upc-scan"></i> Scan</button>
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
</form>
<div class="table-card">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Item</th><th>SKU</th><th>Barcode</th><th>HSN</th><th>GST%</th><th class="text-end">Sale Price</th><th class="text-end">Stock</th><th></th></tr></thead>
        <tbody>
        @foreach($items as $item)
            <tr class="{{ $item->isLowStock() ? 'table-warning' : '' }}">
                <td>{{ $item->name }}</td>
                <td>{{ $item->sku ?? '—' }}</td>
                <td><code class="small">{{ $item->barcode ?? '—' }}</code></td>
                <td>{{ $item->hsn_code ?? '—' }}</td>
                <td>{{ $item->gst_rate }}%</td>
                <td class="text-end">₹{{ number_format($item->sale_price, 2) }}</td>
                <td class="text-end">{{ $item->stock_qty }}</td>
                <td class="text-end"><a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="p-2">{{ $items->links() }}</div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('itemsSearchInput');
    const form = document.getElementById('itemsSearchForm');
    document.getElementById('btnScanItemsList')?.addEventListener('click', () => {
        LaravelRetailBarcode.openCamera((code) => {
            input.value = code;
            form.submit();
        });
    });
    LaravelRetailBarcode.enableWedge((code) => {
        if (document.activeElement === input || document.body.contains(document.activeElement) && !['TEXTAREA'].includes(document.activeElement?.tagName)) {
            input.value = code;
            form.submit();
        }
    });
});
</script>
@endpush
