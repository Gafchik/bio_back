<?php

namespace App\Http\Classes\LogicalModels\User;

use App\Models\MySql\Biodeposit\User_setting;

class UserModel
{
    public function __construct(
        private User_setting $userSetting
    ){}

    public function changeLocale(array $data): void
    {
        $user = $this->userSetting
            ->where('user_id',$data['id'])
            ->update([
                'locale' => $data['lang'],
            ]);
    }
}
