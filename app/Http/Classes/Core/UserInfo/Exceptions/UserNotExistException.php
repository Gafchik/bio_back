<?php

namespace App\Http\Classes\Core\UserInfo\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class UserNotExistException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Доступ запрещен!',
        Lang::UKR => 'Доступ заборонено!',
        Lang::ENG => 'Access denied!',
        Lang::GEO => 'წვდომა აკრძალულია!',
    ];

    protected $code = HttpStatus::HTTP_FORBIDDEN;
}
