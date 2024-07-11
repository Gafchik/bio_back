<?php

namespace App\Http\Controllers\TreeStoreSell;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\TreeStoreSell\TreeStoreSell;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreeStoreSellController extends BaseController
{
    public function __construct(
        private TreeStoreSell $model
    )
    {
        parent::__construct();
    }
    public function getPosition(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'trees' => ['required','array'],
            'trees.*.id' => ['required','integer'],
            'trees.*.sell_amount' => ['required','integer'],
            'trees.*.commission' => ['required','integer'],
        ]);
        return $this->makeGoodResponse(
            $this->model->getPosition($validated)
        );
    }
    public function sell(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'trees' => ['required','array'],
            'trees.*.id' => ['required','integer'],
            'trees.*.sell_amount' => ['required','integer'],
            'trees.*.commission' => ['required','integer'],
        ]);
        try {
            $this->model->sell($validated);
            return $this->makeGoodResponse([]);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
}
