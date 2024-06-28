<?php

namespace App\Http\Controllers\TransactionsHistory;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\TransactionsHistory\TransactionsHistory;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionsHistoryController extends BaseController
{
    public function __construct(
        private TransactionsHistory $model
    )
    {
        parent::__construct();
    }
    public function getTransaction(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getTransaction()
        );
    }
}
