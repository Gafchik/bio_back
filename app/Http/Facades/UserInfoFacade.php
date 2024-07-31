<?php

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;
use App\Http\Classes\Core\UserInfo\UserInfoInterface;
/**
 * @method static array getUserInfo(string $uuid = null,int $id = null);
 * @method static ?array findDemoBalance(string $fieldBySearch, $value, string $uuid = null);
 * @see UserInfoInterface
 */
class UserInfoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user_info_facade';
    }
}
