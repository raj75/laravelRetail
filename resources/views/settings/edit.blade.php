@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<h4 class="mb-3">Business Settings</h4>
<form method="POST" action="{{ route('settings.update') }}">@csrf @method('PUT')
<div class="row g-3">
    <div class="col-lg-8 table-card p-4">
        <div class="row g-3">
            <div class="col-md-6"><label>Business Name</label><input name="business_name" class="form-control" value="{{ $settings->business_name }}" required></div>
            <div class="col-md-6"><label>Legal Name</label><input name="legal_name" class="form-control" value="{{ $settings->legal_name }}"></div>
            <div class="col-md-4"><label>GSTIN</label><input name="gstin" class="form-control" value="{{ $settings->gstin }}"></div>
            <div class="col-md-4"><label>PAN</label><input name="pan" class="form-control" value="{{ $settings->pan }}"></div>
            <div class="col-md-4"><label>Phone</label><input name="phone" class="form-control" value="{{ $settings->phone }}"></div>
            <div class="col-12"><label>Address</label><textarea name="address" class="form-control" rows="2">{{ $settings->address }}</textarea></div>
            <div class="col-md-4"><label>City</label><input name="city" class="form-control" value="{{ $settings->city }}"></div>
            <div class="col-md-4"><label>State</label><input name="state" class="form-control" value="{{ $settings->state }}"></div>
            <div class="col-md-4"><label>Pincode</label><input name="pincode" class="form-control" value="{{ $settings->pincode }}"></div>
            <div class="col-12"><label>Terms & Conditions</label><textarea name="terms_conditions" class="form-control" rows="3">{{ $settings->terms_conditions }}</textarea></div>
            <div class="col-12"><div class="form-check"><input type="checkbox" name="enable_gst" value="1" class="form-check-input" @checked($settings->enable_gst)><label class="form-check-label">Enable GST on invoices</label></div></div>
        </div>
        <button class="btn btn-vyapar mt-3">Save Settings</button>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-3 mb-3">
            <h6>Add Item Category</h6>
            <input name="new_category" class="form-control form-control-sm" placeholder="Category name">
        </div>
        <div class="table-card p-3 mb-3">
            <h6>Add Unit</h6>
            <input name="new_unit_name" class="form-control form-control-sm mb-1" placeholder="Unit name">
            <input name="new_unit_short" class="form-control form-control-sm" placeholder="Short (e.g. Pcs)">
            <p class="small text-muted mt-2 mb-0">Existing: @foreach($units as $u){{ $u->name }}, @endforeach</p>
        </div>
        <div class="table-card p-3">
            <h6>Add Expense Category</h6>
            <input name="new_expense_category" class="form-control form-control-sm" placeholder="e.g. Rent, Salary">
        </div>
    </div>
</div>
</form>
@endsection
