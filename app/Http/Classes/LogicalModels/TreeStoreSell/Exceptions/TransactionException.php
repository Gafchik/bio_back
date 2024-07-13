<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class TransactionException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Ошибка сервера %s',
        Lang::UKR => 'Помилка сервера %s',
        Lang::ENG => 'Server error %s',
        Lang::GEO => 'სერვერის შეცდომა %s',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
