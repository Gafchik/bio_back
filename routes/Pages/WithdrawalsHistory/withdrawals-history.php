<?php

use App\Http\Controllers\WithdrawalsHistory\WithdrawalsHistoryController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'withdrawals-history',
    ],
    function () {
        Route::post('/get', [WithdrawalsHistoryController::class, 'getWithdrawalsHistory']);;
    }
);
