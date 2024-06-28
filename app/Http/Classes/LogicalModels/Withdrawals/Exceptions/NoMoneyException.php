<?php

namespace App\Http\Classes\LogicalModels\Withdrawals\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class NoMoneyException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Не хватает средств!',
        Lang::UKR => 'Бракує коштів!',
        Lang::ENG => 'Not enough funds!',
        Lang::GEO => 'არ არის საკმარისი სახსრები!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
