<?php

use App\Http\Controllers\Purchases\PurchasesController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'purchases',
    ],
    function () {
        Route::post('/get', [PurchasesController::class, 'getPurchases']);;
        Route::post('/get-tree-by-order', [PurchasesController::class, 'getTreeByOrderId']);
        Route::get('/download/{id}', [PurchasesController::class, 'download']);
    }
);
