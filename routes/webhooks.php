<?php

use App\Http\Controllers\Common\WebhooksController;
use Illuminate\Support\Facades\Route;
Route::group([
    'prefix' => 'services/webhooks',
], function () {
    Route::post('stripe',
        [WebhooksController::class, 'stripeWebhook']);
});
