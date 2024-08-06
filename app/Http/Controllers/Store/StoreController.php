<?php

namespace App\Http\Controllers\Store;

use App\Http\Classes\LogicalModels\Store\Store;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends BaseController
{
    public function __construct(
        private Store $model
    )
    {
        parent::__construct();
    }
    public function getTreeStore(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getTreeStore()
        );
    } public function getTreeByYear(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'regex:/^\d{4}$/'],
        ]);
        return $this->makeGoodResponse(
            $this->model->getTreeByYear($validated)
        );
    }
}
