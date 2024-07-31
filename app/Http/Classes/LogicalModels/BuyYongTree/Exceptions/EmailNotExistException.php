<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class EmailNotExistException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'У вас отсутствует Email',
        Lang::UKR => 'У вас немає Email',
        Lang::ENG => "You don't have an email",
        Lang::GEO => 'თქვენ არ გაქვთ ელფოსტა',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
