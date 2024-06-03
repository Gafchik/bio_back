<?php

use App\Http\Controllers\Status\StatusController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'status',
    ],
    function () {
        Route::post('/get', [StatusController::class, 'getStatus']);;
    }
);
