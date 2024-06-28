<?php

use App\Http\Controllers\Withdrawals\WithdrawalsController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'withdrawals',
    ],
    function () {
        Route::post('/fill-request', [WithdrawalsController::class, 'fillReport']);;
    }
);
