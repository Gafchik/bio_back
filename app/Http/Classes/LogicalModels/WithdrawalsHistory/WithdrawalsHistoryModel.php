<?php

namespace App\Http\Classes\LogicalModels\WithdrawalsHistory;

use App\Models\MySql\Biodeposit\Withdraws;
use Illuminate\Support\Facades\Auth;

class WithdrawalsHistoryModel
{
    public function __construct(
        private Withdraws $withdraws,
    ){}

    public function getWithdrawalsHistory(): array
    {
        return $this->withdraws
            ->where('user_id',Auth::user()->id)
            ->orderByDesc('id')
            ->get()
            ->toArray();
    }
}
