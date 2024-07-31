<?php

namespace App\Http\Classes\Core\UserInfo;


interface UserInfoInterface
{
    public function getUserInfo(): array;
    public function findDemoBalance(string $fieldBySearch, $value, string $uuid = null): ?array;
}
