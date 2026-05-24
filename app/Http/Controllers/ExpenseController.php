<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['category', 'account'])->latest('expense_date')->paginate(20);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.form', [
            'expense' => new Expense(['expense_date' => now()->toDateString()]),
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'accounts' => Account::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, AccountService $accountService)
    {
        $data = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'account_id' => ['required', 'exists:accounts,id'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_no' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $expense = Expense::create([...$data, 'user_id' => Auth::id()]);
        $accountService->debit(Account::find($data['account_id']), (float) $data['amount'], Expense::class, $expense->id);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.form', [
            'expense' => $expense,
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'accounts' => Account::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'account_id' => ['required', 'exists:accounts,id'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_no' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
