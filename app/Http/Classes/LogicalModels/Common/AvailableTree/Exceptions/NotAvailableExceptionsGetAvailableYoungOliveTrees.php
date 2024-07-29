<?php

namespace App\Http\Classes\LogicalModels\Common\AvailableTree\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class NotAvailableExceptionsGetAvailableYoungOliveTrees extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Доступно всего: %s единиц!',
        Lang::UKR => 'Доступно усього: %s одиниць!',
        Lang::ENG => 'Total available: %s units!',
        Lang::GEO => 'სულ ხელმისაწვდომია: %s ერთეული!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
