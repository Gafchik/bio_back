<?php

namespace App\Http\Controllers\Gallery;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\Gallery\Gallery;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\Gallery\GetAlbumsDetailsRequest;
use Illuminate\Http\JsonResponse;

class GalleryController extends BaseController
{
    public function __construct(
        private Gallery $model,
    )
    {
        parent::__construct();
    }
    public function getAlbums(): JsonResponse
    {
        return $this->makeGoodResponse(
            $this->model->getAlbums()
        );
    }
    public function getAlbumsDetails(GetAlbumsDetailsRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            return $this->makeGoodResponse(
                $this->model->getAlbumsDetails($data)
            );
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
}
