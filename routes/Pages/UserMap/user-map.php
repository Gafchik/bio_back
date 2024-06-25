<?php

use App\Http\Controllers\UserMap\UserMapController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'user-map',
    ],
    function () {
        Route::post('/get', [UserMapController::class, 'getTrees']);;
    }
);
