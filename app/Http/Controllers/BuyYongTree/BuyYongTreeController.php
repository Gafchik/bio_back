<?php

namespace App\Http\Controllers\BuyYongTree;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\BuyYongTree\BuyYongTree;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyYongTreeController extends BaseController
{
    public function __construct(
        private BuyYongTree $model
    )
    {
        parent::__construct();
    }
    public function getStartInfo(): JsonResponse
    {
        return $this->makeGoodResponse(
            [
                'count' => $this->model->getCountTreeInSell(),
                'price' => $this->model->getPriceYongTree()
            ]
        );
    }
    public function buyBalance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'countTree' => ['required','int','min:1',],
            'promo' => ['nullable','string',],
        ]);
        try {
            $this->model->buyBalance($validated);
            return $this->makeGoodResponse([]);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
}
