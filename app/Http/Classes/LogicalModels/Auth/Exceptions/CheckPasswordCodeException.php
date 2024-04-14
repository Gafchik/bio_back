<?php

namespace App\Http\Classes\LogicalModels\Auth\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class CheckPasswordCodeException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Неправильный код или Email',
        Lang::UKR => 'Невірний код або Email',
        Lang::ENG => 'Incorrect code or email',
        Lang::GEO => 'არასწორი კოდი ან ელფოსტა',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
