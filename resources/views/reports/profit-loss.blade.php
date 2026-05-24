@extends('layouts.app')
@section('title', 'Profit & Loss')
@section('content')
<h4>Profit & Loss</h4>
<form class="row g-2 my-3"><div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm"></div><div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm"></div><div class="col-auto"><button class="btn btn-secondary btn-sm">Filter</button></div></form>
<div class="table-card p-4" style="max-width:400px">
    <div class="d-flex justify-content-between py-2 border-bottom"><span>Sales</span><strong>₹{{ number_format($sales,2) }}</strong></div>
    <div class="d-flex justify-content-between py-2 border-bottom"><span>Purchases</span><strong>₹{{ number_format($purchases,2) }}</strong></div>
    <div class="d-flex justify-content-between py-2 border-bottom"><span>Expenses</span><strong>₹{{ number_format($expenses,2) }}</strong></div>
    <div class="d-flex justify-content-between py-2 fw-bold text-{{ $profit >= 0 ? 'success' : 'danger' }}"><span>Net Profit</span><span>₹{{ number_format($profit,2) }}</span></div>
</div>
@endsection
