@extends('layouts.app')
@section('title', 'Expense')
@section('content')
<div class="table-card p-4" style="max-width:500px">
<form method="POST" action="{{ $expense->exists ? route('expenses.update', $expense) : route('expenses.store') }}">@csrf @if($expense->exists)@method('PUT')@endif
    <div class="mb-2"><label>Category</label><select name="expense_category_id" class="form-select" required>@foreach($categories as $c)<option value="{{ $c->id }}" @selected($expense->expense_category_id==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
    <div class="mb-2"><label>Account</label><select name="account_id" class="form-select" required>@foreach($accounts as $a)<option value="{{ $a->id }}" @selected($expense->account_id==$a->id)>{{ $a->name }}</option>@endforeach</select></div>
    <div class="mb-2"><label>Date</label><input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date?->format('Y-m-d') ?? date('Y-m-d') }}"></div>
    <div class="mb-2"><label>Amount</label><input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->amount }}" required></div>
    <div class="mb-2"><label>Description</label><textarea name="description" class="form-control">{{ $expense->description }}</textarea></div>
    <button class="btn btn-vyapar">Save</button>
</form>
</div>
@endsection
