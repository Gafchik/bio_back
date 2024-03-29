<?php

use App\Http\Controllers\News\NewsController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [],
        'prefix' => 'news',
    ],
    function () {
        Route::post('/get-cards', [NewsController::class, 'getCards']);
        Route::post('/get-cards-details', [NewsController::class, 'getCardsDetails']);
        Route::post('/add-view', [NewsController::class, 'addView']);
    }
);
