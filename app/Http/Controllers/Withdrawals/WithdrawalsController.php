<?php

namespace App\Http\Controllers\Withdrawals;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\Withdrawals\Withdrawals;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawalsController extends BaseController
{
    public function __construct(
        private Withdrawals $model
    )
    {
        parent::__construct();
    }
    public function fillReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required','integer','in:1,2'],
            'amount' => ['required','numeric','min:50'],
            'account_number' => ['required','numeric'],
            'full_name' => ['required', 'string'],
            'phone' => ['nullable','string'],
            'bank' => ['nullable','string'],
        ]);
        try {
            $this->model->fillReport($validated);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
        return $this->makeGoodResponse([]);
    }
}
