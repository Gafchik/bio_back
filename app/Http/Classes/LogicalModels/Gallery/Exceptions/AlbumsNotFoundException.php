<?php

namespace App\Http\Classes\LogicalModels\Gallery\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class AlbumsNotFoundException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Альбом не найден!',
        Lang::UKR => 'Альбом не знайдено!',
        Lang::ENG => 'Album not found!',
        Lang::GEO => 'ალბომი ვერ მოიძებნა!',
    ];

    protected $code = HttpStatus::HTTP_NOT_FOUND;
}
