<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class TreeOnSaleException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s уже на продаже!',
        Lang::UKR => '%s вже на продажі!',
        Lang::ENG => '%s already on sale!',
        Lang::GEO => '%s უკვე იყიდება!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
