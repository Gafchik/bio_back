<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory;

use App\Models\MySql\Biodeposit\{
    Stripe_invoices,
    User_setting,
    Trees,
    Certificates,
    Trees_on_first_sale,
    Trees_on_sale_pack,
    Transactions,
    Details_transactions,
    Wallets,
    Orders,
    Order_details,
};
use App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\EventHandler\{
    StripeWebHookBaseHandler,
    StripeWebHookSuccessHandlerYongTree,
    StripeWebHookExpiredHandler,
};
use App\Http\Classes\Structure\StripeInvoiceStatuses;

class StripeWebHookFactoryYongTree
{
    public static function getEventHandler(string $type): StripeWebHookBaseHandler {
        if($type === StripeInvoiceStatuses::SUCCESS['stripe_name']){
            return new StripeWebHookSuccessHandlerYongTree(
                new Stripe_invoices(),
                new User_setting(),
                new Trees(),
                new Certificates(),
                new Trees_on_first_sale(),
                new Trees_on_sale_pack(),
                new Transactions(),
                new Details_transactions(),
                new Wallets(),
                new Orders(),
                new Order_details(),
            );
        }else{
            return new StripeWebHookExpiredHandler(new Stripe_invoices());
        }
    }
}
