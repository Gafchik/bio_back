<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\Auth\Auth as AuthModel;
use App\Http\Classes\LogicalModels\Auth\Exceptions\UnauthorizedException;
use App\Http\Controllers\BaseControllers\BaseController;
use App\Http\Requests\Auth\AuthCheckEmailRequest;
use App\Http\Requests\Auth\AuthRegRequest;
use App\Http\Requests\Auth\EmailActivateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function __construct(
        private AuthModel $model
    )
    {
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

    public function emailActivate(EmailActivateRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->model->emailActivate($data);
            return $this->makeGoodResponse([]);

        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }

    public function forgotPasswordSendCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255',],
        ]);
        $this->model->forgotPasswordSendCode($validated);
        return $this->makeGoodResponse([]);
    }

    public function checkForgotCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255',],
            'code' => ['required', 'string', 'min:6', 'max:6',],
        ]);
        try {
            $this->model->checkForgotCode($validated);
            return $this->makeGoodResponse([]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255',],
            'code' => ['required', 'string', 'min:6', 'max:6',],
            'password' => [
                'required',
                'confirmed',
                'min:' . config('auth.passwords.min_length'), // 8 length
            ],
        ]);
        try {
            $this->model->changePassword($validated);
            return $this->makeGoodResponse([]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255',],
            'password' => [
                'required',
                'min:' . config('auth.passwords.min_length'), // 8 length
            ],
        ]);
        try {
            $credentials = $request->only('email', 'password');
            $token = Auth::attempt($credentials);
            if(empty($token)){
                throw new UnauthorizedException();
            }
            $user = $this->model->getUserInfo($validated['email']);
            return $this->makeGoodResponse([
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                'userInfo' => $user,
            ]);
        } catch (BaseException $e) {
            return $this->makeBadResponse($e);
        }
    }
    public function getUserInfo(): JsonResponse
    {
        $email = Auth::user()?->email;
        if(empty($email)){
            return $this->makeGoodResponse([]);
        }else{
            $user = $this->model->getUserInfo($email);
            unset($user['secret_key']);
            return $this->makeGoodResponse($user);
        }
    }
    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->makeGoodResponse([]);
    }
    public function checkHas2Fa(): JsonResponse
    {
        $result = $this->model->checkHas2Fa();
        return $this->makeGoodResponse($result);
    }
    public function enable2Fa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);
        try {
            $this->model->enable2Fa($validated);
            return $this->makeGoodResponse([]);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
    public function disable2Fa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);
        try {
            $this->model->disable2Fa($validated);
            return $this->makeGoodResponse([]);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
    }
}
