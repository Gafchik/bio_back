<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory;

use App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\EventHandler\{
    StripeWebHookBaseHandler,
    StripeWebHookExpiredHandler,
    StripeWebHookSuccessHandlerTopUpBalance,
};
use App\Http\Classes\Structure\StripeInvoiceStatuses;
use App\Models\MySql\Biodeposit\{
    Stripe_invoices,
    Transactions,
    Wallets,
};


class StripeWebHookFactoryTopUpBalance
{
    public static function getEventHandler(string $type): StripeWebHookBaseHandler
    {
        if($type === StripeInvoiceStatuses::SUCCESS['stripe_name']){
            return new StripeWebHookSuccessHandlerTopUpBalance(
                new Stripe_invoices(),
                new Transactions(),
                new Wallets(),
            );
        }else{
            return new StripeWebHookExpiredHandler(new Stripe_invoices());
        }
    }
}
