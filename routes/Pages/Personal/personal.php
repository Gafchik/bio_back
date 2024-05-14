<?php

use App\Http\Controllers\Personal\PersonalController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'personal',
    ],
    function () {
        Route::post('/get-trees', [PersonalController::class, 'getTrees']);;
    }
);
