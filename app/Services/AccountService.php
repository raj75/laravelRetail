<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountTransaction;

class AccountService
{
    public function credit(Account $account, float $amount, ?string $refType = null, ?int $refId = null, ?string $desc = null): void
    {
        $account->current_balance = (float) $account->current_balance + $amount;
        $account->save();

        AccountTransaction::create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $account->current_balance,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $desc,
            'transaction_date' => now()->toDateString(),
        ]);
    }

    public function debit(Account $account, float $amount, ?string $refType = null, ?int $refId = null, ?string $desc = null): void
    {
        $account->current_balance = (float) $account->current_balance - $amount;
        $account->save();

        AccountTransaction::create([
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $account->current_balance,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $desc,
            'transaction_date' => now()->toDateString(),
        ]);
    }
}
