<?php

namespace App\Http\Controllers\WithdrawalsHistory;

use App\Http\Classes\LogicalModels\WithdrawalsHistory\WithdrawalsHistory;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;

class WithdrawalsHistoryController extends BaseController
{
    public function __construct(
        private WithdrawalsHistory $model
    ){
        parent::__construct();
    }

    public function getWithdrawalsHistory(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getWithdrawalsHistory()
        );
    }
}
