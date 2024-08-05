<?php

namespace App\Http\Controllers\TopUpWallets;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\TopUpWallets\TopUpWallets;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopUpWalletsController extends BaseController
{
    public function __construct(
        private TopUpWallets $model
    )
    {
        parent::__construct();
    }
    public function topUpStripe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'top_up_amount' => ['required', 'int', 'min:10'],
            'success_url' => ['nullable', 'url'],
            'wallet_type' => ['nullable', 'string']
        ]);
        try {
            $result = $this->model->topUpStripe($validated);
            return $this->makeGoodResponse($result);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function topUpSwift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'top_up_amount' => ['required', 'int', 'min:10'],
            'swift.name' => ['required','string',],
            'swift.company_name' => ['required','string',],
            'swift.address' => ['required','string',],
            'swift.phone' => ['required','string',],
        ]);

        try {
            $this->model->topUpSwift($validated);
            return $this->makeGoodResponse([]);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
}
