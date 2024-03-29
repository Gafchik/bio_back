<?php

namespace App\Http\Controllers\News;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\News\News;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\News\DetailCardRequest;
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
    public function getCardsDetails(DetailCardRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            return $this->makeGoodResponse(
                $this->model->getCardsDetails($data['id'])
            );
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function addView(DetailCardRequest $request)
    {
        $data = $request->validated();
        $this->model->addView($data['id']);
        return $this->makeGoodResponse([]);
    }
}
