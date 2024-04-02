<?php

use App\Http\Controllers\Gallery\GalleryController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [],
        'prefix' => 'gallery',
    ],
    function () {
        Route::post('/get-albums', [GalleryController::class, 'getAlbums']);
        Route::post('/get-albums-details', [GalleryController::class, 'getAlbumsDetails']);
    }
);
