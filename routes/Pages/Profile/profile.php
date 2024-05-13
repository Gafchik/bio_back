<?php

use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'profile',
    ],
    function () {
        Route::post('/change-user-info', [ProfileController::class, 'changeUserInfo']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
    }
);
