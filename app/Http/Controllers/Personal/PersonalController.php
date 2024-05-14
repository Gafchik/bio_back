<?php

namespace App\Http\Controllers\Personal;

use App\Http\Classes\LogicalModels\Personal\Personal;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;

class PersonalController extends BaseController
{
    public function __construct(
        private Personal $model
    )
    {
        parent::__construct();
    }

    public function getTrees(): JsonResponse
    {
        $result = $this->model->getTrees();
        return $this->makeGoodResponse($result);
    }
}
