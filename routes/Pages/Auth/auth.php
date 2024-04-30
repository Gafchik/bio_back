<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'middleware' => [],
        'prefix' => 'auth',
    ],
    function () {
        Route::post('/check-email', [AuthController::class, 'checkEmail']);
        Route::post('/reg', [AuthController::class, 'reg']);
        Route::post('/email-activate', [AuthController::class, 'emailActivate']);
        Route::post('/forgot-password-send-code', [AuthController::class, 'forgotPasswordSendCode']);
        Route::post('/check-code', [AuthController::class, 'checkForgotCode']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/get-user-info', [AuthController::class, 'getUserInfo']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware(['jwt.auth']);
    }
);
