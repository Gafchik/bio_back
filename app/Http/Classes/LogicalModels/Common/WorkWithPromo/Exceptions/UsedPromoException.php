<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class UsedPromoException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Вы уже использовали этот промокод!',
        Lang::UKR => 'Ви вже використали цей промокод!',
        Lang::ENG => "You have already used this promo code!",
        Lang::GEO => 'თქვენ უკვე გამოიყენეთ ეს პრომო კოდი!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
