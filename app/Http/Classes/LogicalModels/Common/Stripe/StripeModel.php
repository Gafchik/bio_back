<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe;

use App\Models\MySql\Biodeposit\Stripe_invoices;

class StripeModel
{
    public function __construct(
        private Stripe_invoices $invoices,
    ){}

    public function getInvoice(string $invoiceId): array
    {
        return $this->invoices
            ->where('invoice_id',$invoiceId)
            ->first()
            ->toArray();
    }
}
