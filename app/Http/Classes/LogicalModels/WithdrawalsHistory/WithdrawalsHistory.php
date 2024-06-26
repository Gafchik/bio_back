<?php

namespace App\Http\Classes\LogicalModels\WithdrawalsHistory;

class WithdrawalsHistory
{
    public function __construct(
        public WithdrawalsHistoryModel $model
    ){}
    public function getWithdrawalsHistory(): array
    {
        return $this->model->getWithdrawalsHistory();
    }
}
