<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class TreeOwnerException  extends BaseException
{
    protected array $langArray = [
        Lang::RUS => 'Не все деревья принадлежат вам!',
        Lang::UKR => 'Не всі дерева належать вам!',
        Lang::ENG => 'Not all trees belong to you!',
        Lang::GEO => 'ყველა ხე შენ არ გეკუთვნის!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
