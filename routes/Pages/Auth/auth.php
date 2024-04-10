<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

//Route::controller(AuthController::class)->group(function () {
//    Route::post('/login', 'login')->name('login');
//
////    Route::post('register', 'register');
//    Route::post('/logout', 'logout')->name('logout');
//    Route::post('/google2fac', 'google2fac')->name('google2fac');
//});
//Route::post('/get-user-info', [AuthController::class,'getUserInfo'])->name('get-user-info');
Route::group(
    [
        'middleware' => [],
        'prefix' => 'auth',
    ],
    function () {
        Route::post('/check-email', [AuthController::class, 'checkEmail']);
        Route::post('/reg', [AuthController::class, 'reg']);
    }
);
