<?php

namespace App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout;


interface StripeTypePaymentCheckoutServiceInterface
{
    public function createCheckoutSession(array $data): array;
    public function prepareDataYongTree(
        array $availableTree,
        int $emptyInvoiceId,
        string $userEmail,
        ?array $promoCode,
        ?string $successUrl
    ): array;
    public function getTtl(): int;
    public function prepareDataTopUpBalance(
        array $user,
        array $data,
        int $emptyInvoiceId,
    ): array;
}
