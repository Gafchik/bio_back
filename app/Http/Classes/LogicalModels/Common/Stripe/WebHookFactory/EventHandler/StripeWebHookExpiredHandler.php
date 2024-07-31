<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\EventHandler;

use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\StripeInvoiceStatuses;
use Stripe\Event;

class StripeWebHookExpiredHandler extends StripeWebHookBaseHandler
{
    public function handle(Event $event): void {
        parent::handle($event);
        $this->updateInvoiceExpired();
    }

    private function updateInvoiceExpired(): void {
        $this->invoices
            ->where('invoice_id',$this->invoiceId)
            ->update([
                'status_id' => StripeInvoiceStatuses::EXPIRED['id'],
                'modified_date' => CDateTime::getCurrentDate(),
            ]);
    }
}
