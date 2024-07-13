<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class LessCommissionException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s слишком маленькая комиссия!',
        Lang::UKR => '%s надто маленька комісія!',
        Lang::ENG => '%s The commission is too small!',
        Lang::GEO => '%s საკომისიო ძალიან მცირეა!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
