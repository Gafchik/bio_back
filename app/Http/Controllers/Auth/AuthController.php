<?php

namespace App\Http\Controllers\Auth;

use App\Http\Classes\LogicalModels\Auth\Auth as AuthModel;
use App\Http\Classes\MailModels\ActivationMail\ActivationMailModel;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\Auth\AuthCheckEmailRequest;
use App\Http\Requests\Auth\AuthRegRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class AuthController extends BaseController
{
    public function __construct(
        private AuthModel $model
    ){
        parent::__construct();
    }
    public function checkEmail(AuthCheckEmailRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->makeGoodResponse([
            'email_use' => $this->model->checkEmail($data)
        ]);
    }
    public function reg(AuthRegRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->makeGoodResponse([
            'is_reg' => $this->model->reg($data)
        ]);
    }
}
