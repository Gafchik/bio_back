<?php

namespace App\Http\Classes\LogicalModels\User\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class UserNotFoundException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Ошибка при получения данных о пользователе',
        Lang::UKR => 'Помилка при отриманні даних про користувача',
        Lang::ENG => 'Error while retrieving user data',
        Lang::GEO => 'შეცდომა მომხმარებლის მონაცემების მიღებისას',
    ];

    protected $code = HttpStatus::HTTP_NOT_FOUND;
}
