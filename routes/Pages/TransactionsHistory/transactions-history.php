<?php

use App\Http\Controllers\TransactionsHistory\TransactionsHistoryController;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'middleware' => ['jwt.auth'],
        'prefix' => 'transactions-history',
    ],
    function () {
        Route::post('/get', [TransactionsHistoryController::class, 'getTransaction']);
    }
);
