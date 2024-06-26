<?php

namespace App\Http\Controllers\UserMap;

use App\Http\Classes\LogicalModels\UserMap\UserMap;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserMapController extends BaseController
{
    public function __construct(
        private UserMap $model
    ){}

    public function getTrees(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getTrees()
        );
    }
}
