<?php

use App\Http\Controllers\Home\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [],
        'prefix' => 'home',
    ],
    function () {
        Route::post('/get-info', [HomeController::class, 'getInfo']);
    }
);
