<?php

use App\Http\Controllers\Insurance\InsuranceController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => [
            'jwt.auth',
            'user_middleware',
        ],
        'prefix' => 'insurance',
    ],
    function () {
        Route::post('/get-insurance-types', [InsuranceController::class, 'getInsuranceTypes']);
        Route::post('/get-insurance-trees', [InsuranceController::class, 'getInsuranceTrees']);
        Route::post('/download', [InsuranceController::class, 'download']);
        Route::post('/create-insurance', [InsuranceController::class, 'createInsurance']);
    }
);
