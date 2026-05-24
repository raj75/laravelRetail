@extends('layouts.app')
@section('title', 'Expenses')
@section('content')
<div class="d-flex justify-content-between mb-3"><h4>Expenses</h4><a href="{{ route('expenses.create') }}" class="btn btn-vyapar btn-sm">Add Expense</a></div>
<div class="table-card"><table class="table mb-0"><thead class="table-light"><tr><th>Date</th><th>Category</th><th>Account</th><th class="text-end">Amount</th></tr></thead>
<tbody>@foreach($expenses as $e)<tr><td>{{ $e->expense_date->format('d/m/Y') }}</td><td>{{ $e->category->name }}</td><td>{{ $e->account->name }}</td><td class="text-end">₹{{ number_format($e->amount,2) }}</td></tr>@endforeach</tbody></table>
<div class="p-2">{{ $expenses->links() }}</div></div>
@endsection
