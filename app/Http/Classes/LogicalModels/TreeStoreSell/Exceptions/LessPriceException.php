<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class LessPriceException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s слишком маленькая цена!',
        Lang::UKR => '%s надто маленька ціна!',
        Lang::ENG => '%s too low price!',
        Lang::GEO => '%s ძალიან დაბალი ფასი!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
