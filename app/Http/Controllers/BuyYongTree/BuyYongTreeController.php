<?php

namespace App\Http\Controllers\BuyYongTree;

use App\Http\Classes\LogicalModels\BuyYongTree\BuyYongTree;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;

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
}
