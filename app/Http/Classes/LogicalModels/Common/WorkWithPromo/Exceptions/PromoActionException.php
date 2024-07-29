<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class PromoActionException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Промокод не соответствует покупке!',
        Lang::UKR => 'Промокод не відповідає покупці!',
        Lang::ENG => "The promo code does not match the purchase!",
        Lang::GEO => 'პრომო კოდი არ ემთხვევა შენაძენს!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
