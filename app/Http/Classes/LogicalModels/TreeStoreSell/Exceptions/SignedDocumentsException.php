<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class SignedDocumentsException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s не завершено оформление!',
        Lang::UKR => '%s не завершено оформлення!',
        Lang::ENG => '%s registration is not completed!',
        Lang::GEO => '%s რეგისტრაცია არ დასრულებულა!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
