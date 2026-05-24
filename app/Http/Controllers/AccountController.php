<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('type')->orderBy('name')->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.form', ['account' => new Account]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['current_balance'] = $data['opening_balance'] ?? 0;
        Account::create($data);

        return redirect()->route('accounts.index')->with('success', 'Account created.');
    }

    public function edit(Account $account)
    {
        return view('accounts.form', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $account->update($this->validated($request));

        return redirect()->route('accounts.index')->with('success', 'Account updated.');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:cash,bank'],
            'bank_name' => ['nullable', 'string'],
            'account_number' => ['nullable', 'string'],
            'ifsc' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
