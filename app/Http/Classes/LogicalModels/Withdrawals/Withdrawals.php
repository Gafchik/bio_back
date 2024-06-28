<?php

namespace App\Http\Classes\LogicalModels\Withdrawals;

use App\Http\Classes\LogicalModels\Withdrawals\Exceptions\NoMoneyException;

class Withdrawals
{
    public function __construct(
        private WithdrawalsModel $model
    ){}
    public function fillReport(array $data): void
    {
        $wallet = $this->model->getWallet();
        $centAmount = $data['amount'] * 100;
        if($wallet['balance'] < $centAmount){
            throw new NoMoneyException();
        }
        $this->model->fillReport($data,$wallet,$centAmount);
    }
}


