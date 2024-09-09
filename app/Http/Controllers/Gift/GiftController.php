<?php

namespace App\Http\Controllers\Gift;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\Gift\Gift;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GiftController extends BaseController
{
    public function __construct(
        private Gift $model
    )
    {
        parent::__construct();
    }

    public function createGift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'int', 'in:2,3'],
            'treesToGift' => ['required', 'array'],
            'treesToGift.*.id' => ['required', 'int', 'exists:' . Trees::class . ',id',],
            'freezeMoneyYear' => ['required', 'int', 'min:0'],
            'freezeSellYear' => ['required', 'int', 'min:3'],
            'isKnowUser' => ['required', 'boolean',],
            'notifyDate' => ['nullable', 'date', 'after_or_equal:today'],
            'email' => [
                Rule::requiredIf(fn() => !!$request->isKnowUser),
                'email',
            ],
        ]);
        try {
            $this->model->createGift($validated);
            return $this->makeGoodResponse([]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
}

