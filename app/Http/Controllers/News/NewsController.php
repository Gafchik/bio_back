<?php

namespace App\Http\Controllers\News;

use App\Http\Classes\LogicalModels\News\News;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\News\NewsCardRequest;
use Illuminate\Http\JsonResponse;

class NewsController extends BaseController
{
    public function __construct(
        private News $model,
    )
    {
        parent::__construct();
    }
    public function getCards(NewsCardRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->makeGoodResponse(
            $this->model->getCards($data['page'])
        );
    }
}
