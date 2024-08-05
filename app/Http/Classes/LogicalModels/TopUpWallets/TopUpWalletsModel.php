<?php

namespace App\Http\Classes\LogicalModels\TopUpWallets;

use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\CurrencyName;
use App\Http\Classes\Structure\InvoiceType;
use App\Http\Classes\Structure\StripeInvoiceStatuses;
use App\Models\MySql\Biodeposit\Stripe_invoices;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_first_sale;
use App\Models\MySql\Biodeposit\User_setting;


class TopUpWalletsModel
{
    public function __construct(
        private User_setting $userSetting,
        private Transactions $transactions,
        private Trees $trees,
        private Trees_on_first_sale $firstSale,
        private Stripe_invoices $invoices,
    ){}

    public function createEmptyInvoiceTopUpBalance(array $user, array $data): int
    {
        return $this->invoices
            ->insertGetId([
                'status_id' => StripeInvoiceStatuses::CREATED['id'],
                'user_id' => $user['id'],
                'invoice_type' => $data['walletType'] ?? null === 'futures'
                    ? InvoiceType::TOP_UP_BALANCE_FUTURES
                    : InvoiceType::TOP_UP_BALANCE,
                'lang' => app()->getLocale(),
                'ccy_id' => CurrencyName::USD['id'],
                'create_date' => CDateTime::getCurrentDate(),
                'modified_date' => CDateTime::getCurrentDate(),
            ]);
    }
    public function updateInvoice(int $emptyInvoiceId, array $stripeResponse, int $ttl): void
    {
        $this->invoices
            ->where('id',$emptyInvoiceId)
            ->update([
                'invoice_id' => $stripeResponse['id'],
                'pay_url' => $stripeResponse['url'],
                'amoute' => $stripeResponse['amount_total'],
                'redirect_success_url' => $stripeResponse['success_url'],
                'ttl' => $ttl,
            ]);
    }
    public function deleteEmptyInvoice(int $emptyInvoiceId): void
    {
        $this->invoices
            ->where('id',$emptyInvoiceId)
            ->delete();
    }
}
