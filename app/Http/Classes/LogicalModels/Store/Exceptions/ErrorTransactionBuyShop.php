<?php

namespace App\Http\Classes\LogicalModels\Store\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class ErrorTransactionBuyShop extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Ошибка при покупке!',
        Lang::UKR => 'Помилка при покупці!',
        Lang::ENG => 'Error when purchasing!',
        Lang::GEO => 'შეცდომა შეძენისას!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
