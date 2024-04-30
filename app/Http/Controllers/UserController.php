<?php

namespace App\Http\Controllers;

use App\Http\Classes\LogicalModels\User\Exceptions\UserNotFoundException;
use App\Http\Classes\LogicalModels\User\User;
use App\Http\Controllers\BaseControllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function __construct(
        private User $model
    )
    {
        parent::__construct();
    }

    public function changeLocale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lang' => ['required', 'string', 'in:en,ge,ru,ua',],
        ]);
        $id = Auth::user()?->id;
        if(empty($id)){
            return $this->makeBadResponse(new UserNotFoundException());
        }else{
            $data['id'] = $id;
            $this->model->changeLocale($data);
            return $this->makeGoodResponse([]);
        }
    }
}
