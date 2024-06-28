<?php

namespace App\Http\Classes\LogicalModels\Withdrawals\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class WithdrawalsSaveException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Ошибка при сохранение заявки попробуйте позже',
        Lang::UKR => 'Помилка при збереженні заявки спробуйте пізніше',
        Lang::ENG => 'Error saving request, try later',
        Lang::GEO => 'შეცდომა მოთხოვნის შენახვისას, სცადეთ მოგვიანებით',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
