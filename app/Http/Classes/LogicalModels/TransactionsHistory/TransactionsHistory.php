<?php

namespace App\Http\Classes\LogicalModels\TransactionsHistory;

class TransactionsHistory
{
    public function __construct(
        private TransactionsHistoryModel $model
    ){}
    public function getTransaction(): array
    {
        $walletIds = $this->model->getWalletIds();
        return $this->model->getTransaction($walletIds);
    }
}
