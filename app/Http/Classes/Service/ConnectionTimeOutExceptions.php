<?php

namespace App\Http\Classes\Service;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\{
    HttpStatus,
    Lang,
};

class ConnectionTimeOutExceptions extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Ошибка подключения к службе: %s, время соединения вышло',
        Lang::UKR => 'Помилка підключення до послуги: %s, тайм-аут з\'єднання',
        Lang::ENG => 'Error connect to service: %s, connection timeout',
        Lang::GEO => 'სერვისთან დაკავშირების შეცდომა: %s, კავშირის დრო ამოიწურა',
    ];

    protected $code = HttpStatus::HTTP_INTERNAL_SERVER_ERROR;
}
