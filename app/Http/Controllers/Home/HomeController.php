<?php

namespace App\Http\Controllers\Home;

use App\Http\Classes\LogicalModels\Home\Home;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;

class HomeController extends BaseController
{
    public function __construct(
        private Home $model,
    )
    {
        parent::__construct();
    }
    public function getInfo(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getInfo()
        );
    }
}
