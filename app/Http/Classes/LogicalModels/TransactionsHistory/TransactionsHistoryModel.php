<?php

namespace App\Http\Classes\LogicalModels\TransactionsHistory;

use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Wallets;
use Illuminate\Support\Facades\Auth;

class TransactionsHistoryModel
{
    public function __construct(
        private Wallets $wallets,
        private Transactions $transactions,
    ){}

    public function getWalletIds(): array
    {
        return $this->wallets
            ->where('user_id',Auth::user()->id)
            ->get()
            ->pluck('id')
            ->toArray();

    }
    public function getTransaction(array $walletIds): array
    {
        return $this->transactions
            ->whereIn('wallet_id', $walletIds)
            ->get()
            ->toArray();
    }
}
