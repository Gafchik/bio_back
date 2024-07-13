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
        Route::post('/get-start-info', [BuyYongTreeController::class, 'getStartInfo']);;
    }
);
