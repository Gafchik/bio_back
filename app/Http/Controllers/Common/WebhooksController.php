<?php

namespace App\Http\Controllers\Common;

use App\Http\Classes\LogicalModels\Common\Stripe\Stripe;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;

class WebhooksController extends BaseController
{
    public function __construct(
        private Stripe $stripeModel
    ){
        parent::__construct();
    }

    public function stripeWebhook(): JsonResponse
    {
        $data = request()->toArray();
        $this->stripeModel->webHook($data);
        return $this->makeGoodResponse([]);
    }
}
