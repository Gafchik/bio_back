<?php

namespace App\Http\Classes\LogicalModels\Gift\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class GiftNotFoundException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Подарок не найден',
        Lang::ENG => 'Gift not found',
        Lang::UKR => 'Подарунок не знайдено',
        Lang::GEO => 'საჩუქარი ვერ მოიძებნა',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
