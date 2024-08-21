<?php

namespace App\Http\Classes\LogicalModels\Insurance\Exceptions;

use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class TreeNotFoundException extends BaseInsuranceException
{
    protected array $langArray = [
        Lang::RUS => 'Информация про страховку не найдена!',
        Lang::UKR => 'Інформація про страховку не знайдено!',
        Lang::ENG => 'Insurance information not found!',
        Lang::GEO => 'სადაზღვევო ინფორმაცია ვერ მოიძებნა!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
