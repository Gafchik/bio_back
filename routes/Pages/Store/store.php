<?php

use App\Http\Controllers\Store\StoreController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [],
        'prefix' => 'tree-store',
    ],
    function () {
        Route::post('/get-tree-store', [StoreController::class, 'getTreeStore']);
        Route::post('/get-tree-by-year', [StoreController::class, 'getTreeByYear']);
    }
);
