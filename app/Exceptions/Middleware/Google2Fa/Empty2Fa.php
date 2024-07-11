<?php

namespace App\Exceptions\Middleware\Google2Fa;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class Empty2Fa extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Отсутствует код двухфакторной аутентификации',
        Lang::UKR => 'Відсутня код двофакторної автентифікації',
        Lang::ENG => 'Two-factor authentication code is missing',
        Lang::GEO => 'ორფაქტორიანი ავთენტიფიკაციის კოდი აკლია',
    ];

    protected $code = HttpStatus::HTTP_FORBIDDEN;
}
