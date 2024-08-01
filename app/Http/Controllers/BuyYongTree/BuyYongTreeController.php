<?php

namespace App\Http\Controllers\BuyYongTree;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\BuyYongTree\BuyYongTree;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
    public function buyBalance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'countTree' => ['required','int','min:1',],
            'promo' => ['nullable','string',],
        ]);
        try {
            $this->model->buyBalance($validated);
            return $this->makeGoodResponse([]);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function buyStripe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'countTree' => ['required','int','min:1',],
            'promo' => ['nullable','string',],
            'success_url' => ['nullable','url',]
        ]);
        try {
            $result = $this->model->buyStripe($validated);
            return $this->makeGoodResponse($result);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function buySwift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'countTree' => ['required','int','min:1',],
            'promo' => ['nullable','string',],
            'swift.name' => ['required','string',],
            'swift.company_name' => ['required','string',],
            'swift.address' => ['required','string',],
            'swift.phone' => ['required','string',],
        ]);
        //TODO посчитать прайс сделать письмо
        foreach (config('emails.swift') as $email) {
            //        Mail::to($email)
//            ->send(new WithdrawalsMailModel([
//                'full_name' => $data['full_name'],
//                'amount' => $centAmount /100,
//            ]));
        }

        dd($validated);
        try {
            $result = $this->model->buyStripe($validated);
            return $this->makeGoodResponse($result);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
}
