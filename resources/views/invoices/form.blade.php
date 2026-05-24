@extends('layouts.app')
@section('title', 'Create ' . (\App\Models\Invoice::TYPES[$type] ?? 'Invoice'))
@section('content')
<h4 class="mb-3">{{ $invoice->exists ? 'Edit' : 'Create' }} {{ \App\Models\Invoice::TYPES[$type] }}</h4>
<div id="barcodeToast" class="alert alert-info py-2 small mb-2 d-none" role="alert"></div>
<form method="POST" action="{{ $invoice->exists ? route('invoices.update', $invoice) : route('invoices.store', $type) }}" id="invoiceForm">
@csrf @if($invoice->exists) @method('PUT') @endif
<div class="table-card p-3 mb-3">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Party</label>
            <select name="party_id" class="form-select" id="party_id">
                <option value="">Walk-in / Cash</option>
                @foreach($parties as $p)<option value="{{ $p->id }}" @selected(old('party_id', $invoice->party_id)==$p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><label class="form-label">Date</label><input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required></div>
        <div class="col-md-2"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}"></div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select"><option value="final" @selected(old('status',$invoice->status ?? 'final')=='final')>Final</option><option value="draft" @selected(old('status',$invoice->status)=='draft')>Draft</option></select>
        </div>
        <div class="col-md-2"><label class="form-label d-block">Inter-state (IGST)</label><input type="checkbox" name="is_inter_state" value="1" class="form-check-input mt-2" @checked(old('is_inter_state', $invoice->is_inter_state))></div>
    </div>
</div>

<div class="table-card p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <h6 class="mb-0">Line Items</h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-success" id="btnScanBarcode" title="Scan with camera or USB scanner">
                <i class="bi bi-upc-scan"></i> Scan Barcode
            </button>
            <button type="button" class="btn btn-sm btn-vyapar" id="addRow"><i class="bi bi-plus"></i> Add Row</button>
        </div>
    </div>
    <p class="small text-muted mb-2"><i class="bi bi-info-circle"></i> Use <strong>Scan Barcode</strong> or plug in a USB scanner — each scan adds a line item automatically.</p>
    <table class="table table-sm" id="itemsTable">
        <thead><tr><th style="width:28%">Item</th><th>HSN</th><th>Qty</th><th>Rate</th><th>Disc</th><th>GST%</th><th>Amount</th><th></th></tr></thead>
        <tbody id="itemRows">
        @php $lines = old('items', $invoice->exists ? $invoice->items->toArray() : [['description'=>'','quantity'=>1,'rate'=>0,'discount'=>0,'gst_rate'=>18]]); @endphp
        @foreach($lines as $idx => $line)
        <tr class="item-row">
            <td>
                <input type="hidden" name="items[{{ $idx }}][item_id]" class="item-id" value="{{ $line['item_id'] ?? '' }}">
                <div class="input-group input-group-sm">
                    <input type="text" name="items[{{ $idx }}][description]" class="form-control item-search" value="{{ $line['description'] ?? '' }}" placeholder="Search or scan…" autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary btn-scan-row" title="Scan"><i class="bi bi-upc-scan"></i></button>
                </div>
            </td>
            <td><input name="items[{{ $idx }}][hsn_code]" class="form-control form-control-sm hsn" value="{{ $line['hsn_code'] ?? '' }}"></td>
            <td><input name="items[{{ $idx }}][quantity]" type="number" step="0.001" class="form-control form-control-sm qty" value="{{ $line['quantity'] ?? 1 }}"></td>
            <td><input name="items[{{ $idx }}][rate]" type="number" step="0.01" class="form-control form-control-sm rate" value="{{ $line['rate'] ?? 0 }}"></td>
            <td><input name="items[{{ $idx }}][discount]" type="number" step="0.01" class="form-control form-control-sm disc" value="{{ $line['discount'] ?? 0 }}"></td>
            <td><input name="items[{{ $idx }}][gst_rate]" type="number" step="0.01" class="form-control form-control-sm gst" value="{{ $line['gst_rate'] ?? 18 }}"></td>
            <td class="line-amount align-middle">0.00</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="table-card p-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $invoice->notes) }}</textarea>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-3">
            <div class="mb-2"><label class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-select form-select-sm">
                    @foreach(['cash','upi','card','bank','cheque'] as $m)<option value="{{ $m }}">{{ ucfirst($m) }}</option>@endforeach
                </select>
            </div>
            <div class="mb-2"><label class="form-label">Account</label>
                <select name="account_id" class="form-select form-select-sm"><option value="">—</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select>
            </div>
            <div class="mb-2"><label class="form-label">Paid Amount</label><input name="paid_amount" type="number" step="0.01" class="form-control form-control-sm" value="{{ old('paid_amount', $invoice->paid_amount) }}"></div>
            <div class="mb-2"><label class="form-label">Invoice Discount</label><input name="discount_amount" type="number" step="0.01" class="form-control form-control-sm" value="{{ old('discount_amount', $invoice->discount_amount) }}"></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>Estimated Total</span><span id="grandTotal">₹0.00</span></div>
            <button type="submit" class="btn btn-vyapar w-100 mt-3">Save Invoice</button>
        </div>
    </div>
</div>
</form>
@endsection
@push('scripts')
<script src="{{ asset('js/invoice-items.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initInvoiceItems({
        initialRowCount: {{ count($lines) }},
        searchUrl: @json(route('items.search')),
        barcodeUrl: @json(route('items.by-barcode')),
        invoiceType: @json($type),
    });
});
</script>
@endpush
