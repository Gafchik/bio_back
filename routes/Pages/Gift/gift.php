<?php
use App\Http\Controllers\Gift\GiftController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'gift',
    ],
    function () {
        Route::post('/create-gift', [GiftController::class, 'createGift']);
    }
);
