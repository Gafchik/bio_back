<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        ['middleware' => ['jwt.auth']],
        'prefix' => 'user',
    ],
    function () {
        Route::post('/change-locale', [UserController::class, 'changeLocale']);
    }
);
