<?php

namespace App\Http\Classes\LogicalModels\News\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class NewsNotFoundException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Новость не найдена!',
        Lang::UKR => 'Новина не знайдена!',
        Lang::ENG => 'News not found!',
        Lang::GEO => 'სიახლე ვერ მოიძებნა!',
    ];

    protected $code = HttpStatus::HTTP_NOT_FOUND;
}
