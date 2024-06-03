<?php

namespace App\Http\Controllers\Status;

use App\Http\Classes\LogicalModels\Status\Status;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;

class StatusController extends BaseController
{
    public function __construct(
        private Status $model
    )
    {
        parent::__construct();
    }
    public function getStatus(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getStatus()
        );
    }
}
