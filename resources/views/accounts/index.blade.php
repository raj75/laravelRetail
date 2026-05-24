@extends('layouts.app')
@section('title', 'Cash & Bank')
@section('content')
<div class="d-flex justify-content-between mb-3"><h4>Cash & Bank Accounts</h4><a href="{{ route('accounts.create') }}" class="btn btn-vyapar btn-sm">Add Account</a></div>
<div class="row g-3">@foreach($accounts as $a)
<div class="col-md-4"><div class="card card-stat p-3"><h6>{{ $a->name }} <span class="badge bg-secondary">{{ $a->type }}</span></h6><h4>₹{{ number_format($a->current_balance,2) }}</h4><a href="{{ route('accounts.edit', $a) }}" class="btn btn-sm btn-link">Edit</a></div></div>
@endforeach</div>
@endsection
