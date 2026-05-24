@extends('layouts.app')
@section('title', 'Record Payment')
@section('content')
<h4 class="mb-3">Record Payment</h4>
<div class="table-card p-4" style="max-width:600px">
<form method="POST" action="{{ route('payments.store') }}">@csrf
    <div class="mb-3"><label>Type</label><select name="type" class="form-select"><option value="receive">Payment In (Receive)</option><option value="pay">Payment Out (Pay)</option></select></div>
    <div class="mb-3"><label>Party</label><select name="party_id" class="form-select" required>@foreach($parties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
    <div class="mb-3"><label>Account</label><select name="account_id" class="form-select" required>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
    <div class="mb-3"><label>Link Invoice (optional)</label><select name="invoice_id" class="form-select"><option value="">—</option>@foreach($invoices as $i)<option value="{{ $i->id }}">{{ $i->invoice_number }} (₹{{ $i->balance_amount }})</option>@endforeach</select></div>
    <div class="row g-2">
        <div class="col-6"><label>Date</label><input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
        <div class="col-6"><label>Amount</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
    </div>
    <div class="mb-3 mt-2"><label>Mode</label><select name="payment_mode" class="form-select">@foreach(['cash','upi','card','bank'] as $m)<option>{{ $m }}</option>@endforeach</select></div>
    <button class="btn btn-vyapar">Save</button>
</form>
</div>
@endsection
