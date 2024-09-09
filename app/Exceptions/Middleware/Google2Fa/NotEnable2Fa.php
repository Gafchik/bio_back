<?php

namespace App\Exceptions\Middleware\Google2Fa;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class NotEnable2Fa extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Не включена двухфакторная аутентификация',
        Lang::UKR => 'Не включена двофакторна автентифікація',
        Lang::ENG => 'Two-factor authentication is not enabled',
        Lang::GEO => 'ორფაქტორიანი ავთენტიფიკაცია არ არის ჩართული',
    ];

    protected $code = HttpStatus::HTTP_FORBIDDEN;
}
