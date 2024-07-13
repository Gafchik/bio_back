<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class FrozenToSaleException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s продажа заморожена до %s',
        Lang::UKR => '%s продаж заморожено до %s',
        Lang::ENG => '%s sale frozen until %s',
        Lang::GEO => '%s გაყიდვა გაყინულია %s',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
