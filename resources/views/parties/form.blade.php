@extends('layouts.app')
@section('title', $party->exists ? 'Edit Party' : 'Add Party')
@section('content')
<h4 class="mb-3">{{ $party->exists ? 'Edit' : 'Add' }} Party</h4>
<div class="table-card p-4" style="max-width:700px">
<form method="POST" action="{{ $party->exists ? route('parties.update', $party) : route('parties.store') }}">
    @csrf @if($party->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Name *</label><input name="name" class="form-control" value="{{ old('name', $party->name) }}" required></div>
        <div class="col-md-4"><label class="form-label">Type</label>
            <select name="type" class="form-select">
                @foreach(['customer','supplier','both'] as $t)<option value="{{ $t }}" @selected(old('type',$party->type)==$t)>{{ ucfirst($t) }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $party->phone) }}"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email', $party->email) }}"></div>
        <div class="col-md-6"><label class="form-label">GSTIN</label><input name="gstin" class="form-control" value="{{ old('gstin', $party->gstin) }}"></div>
        <div class="col-md-6"><label class="form-label">PAN</label><input name="pan" class="form-control" value="{{ old('pan', $party->pan) }}"></div>
        <div class="col-12"><label class="form-label">Billing Address</label><textarea name="billing_address" class="form-control" rows="2">{{ old('billing_address', $party->billing_address) }}</textarea></div>
        <div class="col-md-4"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city', $party->city) }}"></div>
        <div class="col-md-4"><label class="form-label">State</label><input name="state" class="form-control" value="{{ old('state', $party->state) }}"></div>
        <div class="col-md-4"><label class="form-label">Opening Balance</label><input name="opening_balance" type="number" step="0.01" class="form-control" value="{{ old('opening_balance', $party->opening_balance) }}"></div>
    </div>
    <div class="mt-3">
        <button class="btn btn-vyapar">Save</button>
        <a href="{{ route('parties.index') }}" class="btn btn-link">Cancel</a>
    </div>
</form>
</div>
@endsection
