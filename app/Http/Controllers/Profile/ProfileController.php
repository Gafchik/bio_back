<?php

namespace App\Http\Controllers\Profile;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\Profile\Profile;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends BaseController
{
    public function __construct(
        private Profile $model
    ){
        parent::__construct();
    }
    public function changeUserInfo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255',],
            'last_name' => ['required', 'string', 'max:255',],
            'phone' => ['required', 'regex:/[0-9 ]+$/'],
        ]);
        $this->model->changeUserInfo($validated,);
        return $this->makeGoodResponse([]);
    }
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'old_password' => ['required', 'string', 'max:255',],
            'new_password' => ['required','confirmed','min:' . config('auth.passwords.min_length')], // 8 length],
        ]);
        try {
            $this->model->changePassword($validated);
        }catch (BaseException $e){
            return $this->makeBadResponse($e);
        }
        return $this->makeGoodResponse([]);
    }
}
