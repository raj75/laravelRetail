@extends('layouts.app')
@section('content')
<div class="table-card p-4" style="max-width:500px">
<form method="POST" action="{{ $account->exists ? route('accounts.update', $account) : route('accounts.store') }}">@csrf @if($account->exists)@method('PUT')@endif
    <div class="mb-2"><label>Name</label><input name="name" class="form-control" value="{{ $account->name }}" required></div>
    <div class="mb-2"><label>Type</label><select name="type" class="form-select"><option value="cash" @selected($account->type=='cash')>Cash</option><option value="bank" @selected($account->type=='bank')>Bank</option></select></div>
    <div class="mb-2"><label>Opening Balance</label><input name="opening_balance" type="number" step="0.01" class="form-control" value="{{ $account->opening_balance }}"></div>
    <button class="btn btn-vyapar">Save</button>
</form>
</div>
@endsection
