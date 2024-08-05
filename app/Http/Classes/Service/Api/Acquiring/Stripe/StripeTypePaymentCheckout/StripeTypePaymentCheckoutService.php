<?php

namespace App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout;

use App\Http\Classes\Structure\{CDateTime, CurrencyName, HttpStatus};
use Stripe\StripeClient;

class StripeTypePaymentCheckoutService implements StripeTypePaymentCheckoutServiceInterface
{
    public const SERVICE_NAME = 'STRIPE_TYPE_PAYMENT_CHECKOUT';
    public const PERCENT = 100;
    private StripeClient $stripe;
    public function __construct(
         string $token,
         private string $baseSuccessUrl,
         private int $ttl,
    ){
        $this->stripe = new StripeClient($token);
    }

    public function createCheckoutSession(array $data): array
    {
        $data['expires_at'] = $this->ttlToTimestamp(); //ttl

        $checkoutSession = $this->stripe
            ->checkout
            ->sessions
            ->create($data);
        $result = $checkoutSession->toArray();
        return $result;
    }

    public function prepareDataYongTree(
        array $availableTree,
        int $emptyInvoiceId,
        string $userEmail,
        ?array $promoCode,
        ?string $successUrl
    ): array
    {
        $localeData = __('acquiring/stripe/stripe');
        $lianeItems = [];
        foreach ($availableTree as $tree){
            $price = !empty($promoCode)
                ? intval($tree['price'] - ($tree['price']/self::PERCENT) * doubleval($promoCode['promocode_discount']))
                : $tree['price'];
            $lianeItems[] = [
                'price_data' => [
                    'currency' => strtolower(CurrencyName::USD['name']),
                    'product_data' => [
                        'name' => $localeData['name'],
                        'description' => $localeData['destination'],
                        'metadata' => [
                            'tree_id' => $tree['id']
                        ]
                    ],
                    'unit_amount' => $price
                ],
                'quantity' => 1,
            ];
        }
        return [
            'line_items' => $lianeItems,
            'mode' => 'payment',
            'success_url' => $successUrl ?? $this->baseSuccessUrl,
            'client_reference_id' => $emptyInvoiceId,
            'customer_email' => $userEmail,
        ];
    }

    private function ttlToTimestamp(): int {
        $deadInvoiceDate = CDateTime::getCurrentDate();
        $deadInvoiceDate = CDateTime::getDateModified(
            $deadInvoiceDate,
            "+{$this->ttl} seconds"
        );
        return CDateTime::convertDateToTimeStamp($deadInvoiceDate);
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function prepareDataTopUpBalance(
        array $user,
        array $data,
        int $emptyInvoiceId,
    ): array
    {
        $localeData = __('acquiring/stripe/stripe');
        $result = [
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower(CurrencyName::USD['name']),
                        'product_data' => [
                            'name' => $localeData['top_up_balance']['name'],
                            'description' => $localeData['top_up_balance']['destination'],
                        ],
                        'unit_amount' => $data['top_up_amount'] * 100
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $data['success_url'] ?? $this->baseSuccessUrl,
            'client_reference_id' => $emptyInvoiceId,
        ];
        if(!empty($user->email)){
            $result['customer_email'] = $user->email;
        }
        return $result;
    }
}

