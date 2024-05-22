<?php

use App\Http\Controllers\SignedDocuments\SignedDocumentsController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => '/common/signed-documents',
    ],
    function () {
        Route::get('/get-offer/{treeId}', [SignedDocumentsController::class, 'getOfferByUuid']);
        Route::get('/get-contract/{treeId}', [SignedDocumentsController::class, 'getContractByUuid']);
        Route::get('/get-act/{treeId}', [SignedDocumentsController::class, 'getActByUuid']);
        Route::post('/signed', [SignedDocumentsController::class, 'signed']);
    }
);
