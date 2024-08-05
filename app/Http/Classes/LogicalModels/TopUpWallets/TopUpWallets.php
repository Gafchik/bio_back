<?php

namespace App\Http\Classes\LogicalModels\TopUpWallets;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\TopUpWallets\Exceptions\CreatedExceptionStripeTypePaymentCheckout;
use App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout\StripeTypePaymentCheckoutServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TopUpWallets
{
    public function __construct(
        private TopUpWalletsModel $model,
        private StripeTypePaymentCheckoutServiceInterface $stripeService,
    ){}

    public function topUpStripe(array $data)
    {
        $user = Auth::user()->toArray();
        $emptyInvoiceId = $this->model->createEmptyInvoiceTopUpBalance($user,$data);
        $invoiceData = $this->stripeService->prepareDataTopUpBalance(
            $user,
            $data,
            $emptyInvoiceId
        );
        try {
            $stripeResponse = $this->stripeService
                ->createCheckoutSession($invoiceData);
            $this->model->updateInvoice(
                $emptyInvoiceId,
                $stripeResponse,
                $this->stripeService->getTtl(),
            );
        }catch (BaseException $e){
            $this->model->deleteEmptyInvoice($emptyInvoiceId);
            throw new CreatedExceptionStripeTypePaymentCheckout($e->getMessage());
        }
        return [
            'invoice_id' => $stripeResponse['id'],
            'pay_url' => $stripeResponse['url'],
        ];
    }
    public function topUpSwift(array $data)
    {
        foreach (config('emails.swift') as $email) {
            Mail::to($email)
                ->send(new SwiftTopUpWalletsMailModel($data));
        }
    }
}
