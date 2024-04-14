<?php

namespace App\Http\Classes\LogicalModels\Auth\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class ActivationsEmailNotFoundException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Email не найден',
        Lang::UKR => 'Email не знайдено',
        Lang::ENG => 'Email not found',
        Lang::GEO => 'ელფოსტა ვერ მოიძებნა',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
