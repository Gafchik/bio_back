<?php

namespace App\Exceptions\Middleware\Google2Fa;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class Incorrect2Fa extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Неправильный код двухфакторной аутентификации',
        Lang::UKR => 'Неправильний код двофакторної автентифікації',
        Lang::ENG => 'Incorrect two-factor authentication code',
        Lang::GEO => 'არასწორი ორფაქტორიანი ავთენტიფიკაციის კოდი',
    ];

    protected $code = HttpStatus::HTTP_FORBIDDEN;
}
