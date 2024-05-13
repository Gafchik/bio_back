<?php

namespace App\Http\Classes\LogicalModels\Profile;

use App\Http\Classes\LogicalModels\Profile\Exceptions\NotValidCurrentPasswordException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile
{
    public function __construct(
        private ProfileModel $model,
    ){}
    public function changeUserInfo(array $date): void
    {
        $this->model->changeUserInfo($date);
    }
    public function changePassword(array $date): void
    {
        if (!Hash::check($date['old_password'], Auth::user()->password)) {
            throw new NotValidCurrentPasswordException();
        }
        $this->model->changePassword($date['new_password']);
    }
}
