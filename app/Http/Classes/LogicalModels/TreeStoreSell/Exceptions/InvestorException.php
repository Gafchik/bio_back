<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class InvestorException extends BaseException
{
    protected array $langArray = [
        Lang::RUS => '%s молодое дерево!',
        Lang::UKR => '%s молоде дерево!',
        Lang::ENG => '%s young tree!',
        Lang::GEO => '%s ახალგაზრდა ხე!',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
