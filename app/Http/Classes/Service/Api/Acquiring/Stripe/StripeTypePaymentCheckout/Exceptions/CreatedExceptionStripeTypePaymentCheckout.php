<?php

namespace App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout\Exceptions;

use App\Http\Classes\Service\Api\Acquiring\Stripe\StripeBaseException;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\Classes\Structure\Lang;

class CreatedExceptionStripeTypePaymentCheckout extends StripeBaseException
{
    protected array $langArray = [
        Lang::RUS => 'Ошибка при создание платежа: %s',
        Lang::ENG => 'Error creating payment: %s',
        Lang::UKR => 'Помилка під час створення платежу: %s',
        Lang::GEO => 'შეცდომა გადახდის შექმნისას: %s',
    ];

    protected $code = HttpStatus::HTTP_UNPROCESSABLE_ENTITY;
}
