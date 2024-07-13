<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class MoreCommissionException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s слишком большая комиссия!',
        Lang::UKR => '%s надто велика комісія!',
        Lang::ENG => '%s too much commission!',
        Lang::GEO => '%s ძალიან ბევრი საკომისიო!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
