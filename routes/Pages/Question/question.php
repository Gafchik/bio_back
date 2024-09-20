<?php
use App\Http\Controllers\Question\QuestionController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [],
        'prefix' => 'question',
    ],
    function () {
        Route::post('/send', [QuestionController::class, 'sendQuestion']);
    }
);
