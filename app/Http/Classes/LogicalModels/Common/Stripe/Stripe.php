<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe;

use App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\StripeWebHookFactoryTopUpBalance;
use App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\StripeWebHookFactoryYongTree;
use App\Http\Classes\Structure\InvoiceType;
use Stripe\Event;

class Stripe
{
    public function __construct(
        public StripeModel $model,
    ){}

    public function webhook(array $data): void
    {
        $event = Event::constructFrom($data);
        $invoiceId = $event->data->object->id;
        $invoice = $this->model->getInvoice($invoiceId);
        if(
            $invoice['invoice_type'] === InvoiceType::TOP_UP_BALANCE
            || $invoice['invoice_type'] === InvoiceType::TOP_UP_BALANCE_FUTURES
        ){
            $handler = StripeWebHookFactoryTopUpBalance::getEventHandler($event->type);
        }else{
            $handler = StripeWebHookFactoryYongTree::getEventHandler($event->type);
        }
        $handler->handle($event);
    }
}
