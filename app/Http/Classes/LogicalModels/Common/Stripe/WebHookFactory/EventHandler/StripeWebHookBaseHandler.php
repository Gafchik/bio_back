<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\EventHandler;

use App\Models\MySql\Biodeposit\Stripe_invoices;
use Stripe\Event;

abstract class StripeWebHookBaseHandler
{
    public function __construct(
         protected Stripe_invoices $invoices,
    ){}
    protected string $invoiceId;
    public function handle(Event $event): void {
        $this->invoiceId = $event->data->object->id;
    }
}
