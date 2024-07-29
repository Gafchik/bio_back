<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class CountTreesException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Количество запрашиваемых сертификатов не соответствует промокоду!',
        Lang::UKR => 'Кількість сертифікатів, що запитуються, не відповідає промокоду!',
        Lang::ENG => "The number of certificates requested does not match the promo code!",
        Lang::GEO => 'მოთხოვნილი სერთიფიკატების რაოდენობა არ ემთხვევა პრომო კოდს!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
