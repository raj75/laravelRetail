@extends('layouts.app')
@section('title', 'Party Ledger')
@section('content')
<h4>Party Ledger</h4>
<form class="mb-3"><select name="party_id" class="form-select w-50 d-inline" onchange="this.form.submit()"><option value="">Select party</option>@foreach($parties as $p)<option value="{{ $p->id }}" @selected(request('party_id')==$p->id)>{{ $p->name }}</option>@endforeach</select></form>
@if($party)
<h5>{{ $party->name }} — Balance ₹{{ number_format($party->current_balance,2) }}</h5>
<div class="table-card mt-3"><table class="table mb-0"><thead><tr><th>Date</th><th>Ref</th><th class="text-end">Amount</th></tr></thead>
<tbody>@foreach($invoices as $i)<tr><td>{{ $i->invoice_date->format('d/m/Y') }}</td><td>{{ $i->invoice_number }}</td><td class="text-end">₹{{ number_format($i->total_amount,2) }}</td></tr>@endforeach
@foreach($payments as $p)<tr><td>{{ $p->payment_date->format('d/m/Y') }}</td><td>Payment {{ $p->payment_number }}</td><td class="text-end">₹{{ number_format($p->amount,2) }}</td></tr>@endforeach</tbody></table></div>
@endif
@endsection
