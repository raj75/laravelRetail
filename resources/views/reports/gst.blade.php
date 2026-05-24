@extends('layouts.app')
@section('title', 'GST Report')
@section('content')
<h4>GST Summary</h4>
<form class="row g-2 my-3"><div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm"></div><div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm"></div><div class="col-auto"><button class="btn btn-secondary btn-sm">Filter</button></div></form>
<div class="row g-3">
    <div class="col-md-6 table-card p-3"><h6>Sales (Output GST)</h6>
        <p>Taxable: ₹{{ number_format($salesGst->taxable ?? 0, 2) }}</p>
        <p>CGST: ₹{{ number_format($salesGst->cgst ?? 0, 2) }} | SGST: ₹{{ number_format($salesGst->sgst ?? 0, 2) }} | IGST: ₹{{ number_format($salesGst->igst ?? 0, 2) }}</p>
    </div>
    <div class="col-md-6 table-card p-3"><h6>Purchase (Input GST)</h6>
        <p>Taxable: ₹{{ number_format($purchaseGst->taxable ?? 0, 2) }}</p>
        <p>CGST: ₹{{ number_format($purchaseGst->cgst ?? 0, 2) }} | SGST: ₹{{ number_format($purchaseGst->sgst ?? 0, 2) }} | IGST: ₹{{ number_format($purchaseGst->igst ?? 0, 2) }}</p>
    </div>
</div>
@endsection
