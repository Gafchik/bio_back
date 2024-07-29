<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class MaxTreesExceptionStripe extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'При этой форме оплаты максимальное количество: %s',
        Lang::UKR => 'При цій формі оплати максимальна кількість: %s',
        Lang::ENG => "With this form of payment the maximum quantity is: %s",
        Lang::GEO => 'გადახდის ამ ფორმით მაქსიმალური რაოდენობაა: %s',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
