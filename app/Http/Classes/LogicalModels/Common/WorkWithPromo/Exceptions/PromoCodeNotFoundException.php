<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class PromoCodeNotFoundException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Промокода не найден!',
        Lang::UKR => 'Промокод не знайдено!',
        Lang::ENG => "Promo code not found!",
        Lang::GEO => 'პრომო კოდი ვერ მოიძებნა!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
