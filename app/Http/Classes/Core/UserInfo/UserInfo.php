<?php

namespace App\Http\Classes\Core\UserInfo;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Classes\Structure\CustomHeaders;
use App\Models\MySql\Biodeposit\{
    Users,
};

class UserInfo implements UserInfoInterface
{


    public function __construct(
        private UserInfoModel $model,
    ){}


    public function getUserInfo(
        string $uuid = null,
        int $id = null,
    ): array
    {
        if(!is_null($uuid)){
            return $this->model->getUserInfo(uuid: $uuid);
        }
        if(!is_null($id)){
            return $this->model->getUserInfo(id:$id);
        }
        return [];
    }

    public function findDemoBalance(string $fieldBySearch, $value, string $uuid = null): ?array
    {
        $user = is_null($uuid)
            ? $this->getUserInfo()
            : $this->getUserInfo($uuid);

        return TransformArrayHelper::callbackSearchFirstInArray(
            $user['demoBalance'],
            fn($el) => $el[$fieldBySearch] === ($value)
        );
    }
}
