<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\EventHandler;

use App\Http\Classes\Structure\{CDateTime,
    CurrencyName,
    InvoiceType,
    Payments,
    StripeInvoiceStatuses,
    TransactionTypes};
use App\Http\Facades\UserInfoFacade;
use Illuminate\Support\Facades\DB;
use App\Models\MySql\Biodeposit\{
    Stripe_invoices,
    Transactions,
    Wallets,
};
use Stripe\Event;

class StripeWebHookSuccessHandlerTopUpBalance extends StripeWebHookBaseHandler
{
    public function __construct(
        Stripe_invoices $invoices,
        private Transactions $transactions,
        private Wallets $wallets,
    )
    {
        parent::__construct($invoices);
    }

    public function handle(Event $event): void {
        parent::handle($event);
        $invoice = $this->getInvoice();
        $this->updateInvoiceBySuccess();
        $user = UserInfoFacade::getUserInfo(id:$invoice['user_id']);
        $this->insertInToTransactions($user,$invoice);
        $this->topUpBalance($user,$invoice);
    }
    private function getInvoice(): array
    {
        return $this->invoices
            ->where('invoice_id',$this->invoiceId)
            ->first()
            ->toArray();
    }
    private function updateInvoiceBySuccess(): void
    {
        $this->invoices
            ->where('invoice_id',$this->invoiceId)
            ->update([
                'status_id' => StripeInvoiceStatuses::SUCCESS['id'],
                'modified_date' => CDateTime::getCurrentDate(),
            ]);
    }
    private function insertInToTransactions(
        array $user,
        array $invoice,
    ): void
    {
         $this->transactions
            ->insertGetId([
                'wallet_id' => $user['walletLivePayId'],
                'type' => TransactionTypes::TOP_UP_BALANCE,
                'amount' => $invoice['amoute'],
                'commission' => 0,
                'total' => $invoice['amoute'],
                'tree_count' => 0,
                'status' => 1, //success in old transaction code
                'payment_service' => Payments::STRIPE['id'],
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => CurrencyName::USD['name'],
            ]);
    }
    private function topUpBalance(array $user, array $invoice): void
    {
        if($invoice['invoice_type'] === InvoiceType::TOP_UP_BALANCE){
            $this->topUpBaseWallet($user,$invoice);
        }
        if($invoice['invoice_type'] === InvoiceType::TOP_UP_BALANCE_FUTURES){
            $this->topUpFuturesWallet($user,$invoice);
        }
    }
    private function topUpBaseWallet(array $user, array $invoice): void
    {
        $this->wallets
            ->where('id',$user['walletLivePayId'])
            ->update([
                'balance' => DB::raw('balance + '.$invoice['amoute'])
            ]);
    }
    private function topUpFuturesWallet(array $user, array $invoice): void
    {
        $amoute = ($invoice['amoute'] / 110) * 144;
        $this->wallets
            ->where('id',$user['walletFuturesPayId'])
            ->update([
                'balance' => DB::raw('balance + '.$amoute)
            ]);
    }

}
