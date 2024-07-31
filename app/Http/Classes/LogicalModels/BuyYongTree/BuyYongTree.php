<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree;

use App\Http\Classes\LogicalModels\BuyYongTree\Exceptions\EmailNotExistException;
use App\Http\Classes\LogicalModels\BuyYongTree\Exceptions\MaxTreesExceptionStripe;
use App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout\Exceptions\CreatedExceptionStripeTypePaymentCheckout;
use App\Http\Classes\Service\Api\Acquiring\Stripe\StripeTypePaymentCheckout\StripeTypePaymentCheckoutServiceInterface;
use App\Http\Classes\Structure\HttpStatus;
use App\Http\DTO\Core\MongoLog\MongoLogDTO;
use App\Http\Facades\AvailableTreeFacade;
use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Facades\MongoLogFacade;
use App\Http\Facades\WorkWithPromoFacade;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\InvalidRequestException;

class BuyYongTree
{
    private const MAX_TREES = 100;
    public function __construct(
        private BuyYongTreeModel $model,
        private StripeTypePaymentCheckoutServiceInterface $stripeService,
    ){}
    public function getCountTreeInSell(): int
    {
        return $this->model->getCountTreeInSell();
    }
    public function getPriceYongTree(): int
    {
        return $this->model->getPriceYongTree();
    }
    public function buyBalance(array $data): void
    {
        $user = Auth::user()->toArray();
        throw_if(
            !$user['email'],
            new EmailNotExistException()
        );
        throw_if(
            $data['countTree'] > self::MAX_TREES,
            new MaxTreesExceptionStripe(self::MAX_TREES)
        );
        $availableTree = AvailableTreeFacade::getAvailableYoungOliveTrees($data['countTree']);
        $promoCode = !empty($data['promo'])
            ? WorkWithPromoFacade::workWithPromo($data['promo'],count($availableTree))
            : null;
        $treeIds = TransformArrayHelper::getArrayUniqueByField(
            $availableTree, 'id'
        );
        $groupArray = $this->model->getGroupArrayForTransactionDetail($treeIds);
        $allData = [
            'user' => $user,
            'wallets' => $this->model->getWallets($user['id']),
            'availableTree' => $availableTree,
            'treeIds' => $treeIds,
            'groupArray' => $groupArray,
            'price' => $this->getPrice($availableTree,$promoCode),
            'promoCode' => $promoCode,
            'salePackIds' => $this->model->getSalePackIds($treeIds),
        ];
        $this->model->buyBalance($allData);
    }
    private function getPrice(array $availableTree,?array $promoCode): float
    {
        $price = 0;
        foreach ($availableTree as $tree) {
            $price += !empty($promoCode)
                ? intval($tree['price'] - ($tree['price'] / 100) * doubleval($promoCode['promocode_discount']))
                : $tree['price'];
        }
        return $price;
    }
    public function buyStripe(array $data): array
    {
        $user = Auth::user()->toArray();
        throw_if(
            !$user['email'],
            new EmailNotExistException()
        );
        throw_if(
            $data['countTree'] > self::MAX_TREES,
            new MaxTreesExceptionStripe(self::MAX_TREES)
        );
        $availableTree = AvailableTreeFacade::getAvailableYoungOliveTrees($data['countTree']);
        $promoCode = !empty($data['promo'])
            ? WorkWithPromoFacade::workWithPromo($data['promo'],count($availableTree))
            : null;
        $treeIds = TransformArrayHelper::getArrayUniqueByField(
            $availableTree, 'id'
        );
        $this->model->setPendingStatus($treeIds);
        $emptyInvoiceId = $this->model->createEmptyInvoiceYongTree(
            $treeIds,
            $user['id'],
            $promoCode,
        );
        $invoiceData = $this->stripeService->prepareDataYongTree(
            availableTree: $availableTree,
            emptyInvoiceId: $emptyInvoiceId,
            userEmail: $user['email'],
            promoCode: $promoCode,
            successUrl: $data['success_url'] ?? null
        );
        try {
            $stripeResponse = $this->stripeService->createCheckoutSession($invoiceData);
            $this->model->updateInvoice(
                $emptyInvoiceId,
                $stripeResponse,
                $this->stripeService->getTtl(),
            );
        }catch (InvalidRequestException $e){
            $this->model->deleteEmptyInvoice($emptyInvoiceId);
            $this->model->setPendingStatus($treeIds, false);
            throw new CreatedExceptionStripeTypePaymentCheckout($e->getMessage());
        }
        return [
            'invoice_id' => $stripeResponse['id'],
            'pay_url' => $stripeResponse['url'],
        ];
    }

}
