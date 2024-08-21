<?php

namespace App\Http\Classes\LogicalModels\Insurance\Exceptions;

use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class RequestAlreadyExistException extends BaseInsuranceException
{
    protected array $langArray = [
        Lang::RUS => 'Заявка уже подана!',
        Lang::UKR => 'Заявка вже подана!',
        Lang::ENG => 'The request has already been submitted!',
        Lang::GEO => 'განაცხადი უკვე შეტანილია!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
