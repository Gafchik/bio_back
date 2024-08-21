<?php

namespace App\Http\Classes\LogicalModels\Insurance\Exceptions;

use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class NoMoneyException extends BaseInsuranceException
{
    protected array $langArray = [
        Lang::RUS => 'Не достаточно средств на балансе!',
        Lang::UKR => 'Мало коштів на балансі!',
        Lang::ENG => 'Not enough funds on balance!',
        Lang::GEO => 'არ არის საკმარისი თანხა ბალანსზე!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
