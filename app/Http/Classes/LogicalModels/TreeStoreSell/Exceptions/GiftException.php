<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class GiftException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s это подарок!',
        Lang::UKR => '%s це подарунок!',
        Lang::ENG => '%s this is a gift!',
        Lang::GEO => '%s ეს არის საჩუქარი!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
