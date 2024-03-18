<?php

use App\Http\Controllers\Contacts\ContactsController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [],
        'prefix' => 'contacts',
    ],
    function () {
        Route::post('/get', [ContactsController::class, 'getContacts']);
    }
);
