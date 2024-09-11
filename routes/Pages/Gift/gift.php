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
        Route::post('/get-gift-info', [GiftController::class, 'getGiftInfo']);
        Route::post('/cancel-my-gift', [GiftController::class, 'cancelMyGift']);
        Route::post('/download-gift-certificate', [GiftController::class, 'downloadGiftCertificate']);
        Route::post('/get-gift-by-code', [GiftController::class, 'getGiftByCode']);
    }
);
