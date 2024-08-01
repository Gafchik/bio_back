<?php

use App\Http\Controllers\BuyYongTree\BuyYongTreeController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'buy-yong-tree',
    ],
    function () {
        Route::post('/get-start-info', [BuyYongTreeController::class, 'getStartInfo']);
        Route::post('/buy-balance', [BuyYongTreeController::class, 'buyBalance']);
        Route::post('/buy-stripe', [BuyYongTreeController::class, 'buyStripe']);
        Route::post('/buy-swift', [BuyYongTreeController::class, 'buySwift']);
    }
);
