<?php

use App\Http\Controllers\TreeStoreSell\TreeStoreSellController;
use App\Http\Middleware\Google2FaMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'tree-store-sell',
    ],
    function () {
        Route::post('/get-position', [TreeStoreSellController::class, 'getPosition']);;
        Route::post('/sell', [TreeStoreSellController::class, 'sell'])
            ->middleware(Google2FaMiddleware::class);
        Route::post('/remove-sell', [TreeStoreSellController::class, 'removeSell'])
            ->middleware(Google2FaMiddleware::class);
        Route::post('/get-tree-in-sell',[TreeStoreSellController::class, 'getTreeInSell']);
    }
);
