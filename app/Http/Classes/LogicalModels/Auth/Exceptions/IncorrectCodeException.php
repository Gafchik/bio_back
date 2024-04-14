<?php

namespace App\Http\Classes\LogicalModels\Auth\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class IncorrectCodeException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Не верный код',
        Lang::UKR => 'Невірний код',
        Lang::ENG => 'Incorrect code',
        Lang::GEO => 'არასწორი კოდი',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
