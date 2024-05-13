<?php

namespace App\Http\Classes\LogicalModels\Profile\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class NotValidCurrentPasswordException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Неверный текущий пароль!',
        Lang::UKR => 'Неправильний поточний пароль!',
        Lang::GEO => 'მიმდინარე პაროლი არასწორია!',
        Lang::ENG => 'Invalid current Password!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
