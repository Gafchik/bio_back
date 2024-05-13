<?php

namespace App\Http\Classes\LogicalModels\Auth\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class UnauthorizedException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Неверный Email или пароль!',
        Lang::UKR => 'Неправильний Email або пароль!',
        Lang::GEO => 'Არასწორი ელექტრონული ფოსტა ან პაროლი!',
        Lang::ENG => 'Invalid Email or Password!',
    ];

    protected $code = HttpStatus::HTTP_UNAUTHORIZED;
}
