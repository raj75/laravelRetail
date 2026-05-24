@extends('layouts.app')
@section('title', 'Item')
@section('content')
<h4 class="mb-3">{{ $item->exists ? 'Edit' : 'Add' }} Item</h4>
<div id="barcodeToast" class="alert alert-info py-2 small mb-2 d-none" role="alert"></div>
<div class="table-card p-4">
<form method="POST" action="{{ $item->exists ? route('items.update', $item) : route('items.store') }}" id="itemForm">
    @csrf @if($item->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Name *</label><input name="name" id="itemName" class="form-control" value="{{ old('name', $item->name) }}" required></div>
        <div class="col-md-3"><label class="form-label">SKU</label><input name="sku" id="itemSku" class="form-control" value="{{ old('sku', $item->sku) }}"></div>
        <div class="col-md-3">
            <label class="form-label">Barcode</label>
            <div class="input-group">
                <input name="barcode" id="itemBarcode" class="form-control" value="{{ old('barcode', $item->barcode) }}" placeholder="Scan or type">
                <button type="button" class="btn btn-outline-success" id="btnScanItemBarcode" title="Scan barcode"><i class="bi bi-upc-scan"></i></button>
            </div>
            <div class="form-text">Scan to assign barcode; duplicates are checked on save.</div>
        </div>
        <div class="col-md-4"><label class="form-label">Category</label><select name="category_id" class="form-select"><option value="">—</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id',$item->category_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Unit</label><select name="unit_id" class="form-select"><option value="">—</option>@foreach($units as $u)<option value="{{ $u->id }}" @selected(old('unit_id',$item->unit_id)==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">HSN</label><input name="hsn_code" id="itemHsn" class="form-control" value="{{ old('hsn_code', $item->hsn_code) }}"></div>
        <div class="col-md-3"><label class="form-label">Purchase Price</label><input name="purchase_price" id="itemPurchasePrice" type="number" step="0.01" class="form-control" value="{{ old('purchase_price', $item->purchase_price) }}"></div>
        <div class="col-md-3"><label class="form-label">Sale Price</label><input name="sale_price" id="itemSalePrice" type="number" step="0.01" class="form-control" value="{{ old('sale_price', $item->sale_price) }}"></div>
        <div class="col-md-2"><label class="form-label">GST %</label><input name="gst_rate" id="itemGst" type="number" step="0.01" class="form-control" value="{{ old('gst_rate', $item->gst_rate ?? 18) }}"></div>
        <div class="col-md-2"><label class="form-label">Stock Qty</label><input name="stock_qty" type="number" step="0.001" class="form-control" value="{{ old('stock_qty', $item->stock_qty) }}"></div>
        <div class="col-md-2"><label class="form-label">Low Stock Alert</label><input name="low_stock_alert" type="number" step="0.001" class="form-control" value="{{ old('low_stock_alert', $item->low_stock_alert ?? 5) }}"></div>
        <div class="col-md-12"><div class="form-check"><input type="checkbox" name="track_inventory" value="1" class="form-check-input" @checked(old('track_inventory', $item->track_inventory ?? true))><label class="form-check-label">Track inventory</label></div></div>
    </div>
    <button class="btn btn-vyapar mt-3">Save Item</button>
</form>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const barcodeInput = document.getElementById('itemBarcode');
    const toast = document.getElementById('barcodeToast');
    const barcodeUrl = @json(route('items.by-barcode'));
    const currentId = @json($item->id);

    function showToast(msg, type) {
        toast.className = 'alert alert-' + type + ' py-2 small mb-2';
        toast.textContent = msg;
        toast.classList.remove('d-none');
        setTimeout(() => toast.classList.add('d-none'), 4000);
    }

    async function onBarcodeScanned(code) {
        barcodeInput.value = code;
        try {
            const item = await LaravelRetailBarcode.lookupBarcode(barcodeUrl, code);
            if (currentId && item.id === currentId) {
                showToast('Barcode set on this item.', 'success');
                return;
            }
            if (item.id) {
                showToast('Barcode already used by: ' + item.name + '. Choose another.', 'warning');
                return;
            }
        } catch (e) {
            showToast('New barcode: ' + code, 'success');
        }
    }

    document.getElementById('btnScanItemBarcode').addEventListener('click', () => {
        LaravelRetailBarcode.openCamera(onBarcodeScanned);
    });

    LaravelRetailBarcode.enableWedge((code) => {
        if (document.activeElement === barcodeInput || !['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement?.tagName)) {
            onBarcodeScanned(code);
        }
    });
});
</script>
@endpush
