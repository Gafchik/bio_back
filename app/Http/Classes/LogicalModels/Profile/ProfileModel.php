<?php

namespace App\Http\Classes\LogicalModels\Profile;

use App\Http\Classes\Helpers\PasswordHashHelper;
use App\Models\MySql\Biodeposit\UserInfo;
use App\Models\MySql\Biodeposit\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileModel
{
    public function __construct(
        private UserInfo $userInfo,
        private Users $users,
    ){}

    public function changeUserInfo(array $date): void
    {
        $this->userInfo
            ->where('user_id', Auth::user()->id)
            ->update($date);
    }
    public function changePassword(string $password): void
    {
        $this->users
            ->where('id', Auth::user()->id)
            ->update([
                'password' => PasswordHashHelper::generatePasswordHash($password)
            ]);
    }
}
