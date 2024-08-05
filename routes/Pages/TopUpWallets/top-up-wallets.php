<?php

use App\Http\Controllers\TopUpWallets\TopUpWalletsController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'top-up-wallet',
    ],
    function () {
        Route::post('/stripe', [TopUpWalletsController::class, 'topUpStripe']);
        Route::post('/swift', [TopUpWalletsController::class, 'topUpSwift']);
    }
);
