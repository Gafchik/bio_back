<?php

namespace App\Providers\ServicesAPI\Acquiring\Stripe;

use App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout\{
    StripeTypePaymentCheckoutService,
    StripeTypePaymentCheckoutServiceInterface,
};
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class StripeProvider extends ServiceProvider
{
    private const PATH_TO_CONFIG = 'acquiring.stripe.';
    //Stripe Checkout
    public function register() {
        $this->app->singleton(StripeTypePaymentCheckoutServiceInterface::class, function () {
            return $this->app->make(StripeTypePaymentCheckoutService::class, [
                'token' => Config::get(self::PATH_TO_CONFIG.'token'),
                'baseSuccessUrl' => Config::get(self::PATH_TO_CONFIG.'token'),
                'ttl' => Config::get(self::PATH_TO_CONFIG.'ttl'),
            ]);
        });
    }
}
