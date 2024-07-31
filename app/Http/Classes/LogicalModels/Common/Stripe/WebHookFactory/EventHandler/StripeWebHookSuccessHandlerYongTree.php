<?php

namespace App\Http\Classes\LogicalModels\Common\Stripe\WebHookFactory\EventHandler;

use Illuminate\Support\Facades\DB;
use App\Http\Classes\Structure\{
    StripeInvoiceStatuses,
    TransactionTypes,
    CDateTime,
    CurrencyName,
    Payments,
    TreeGiftStatus,
    TreeSaleStatus
};
use App\Http\Facades\UserInfoFacade;
use App\Models\MySql\Biodeposit\{
    Certificates,
    Details_transactions,
    Order_details,
    Orders,
    Stripe_invoices,
    Transactions,
    Trees,
    Trees_on_first_sale,
    Trees_on_sale_pack,
    User_setting,
    Wallets
};
use Stripe\Event;
use Webpatser\Uuid\Uuid;

class StripeWebHookSuccessHandlerYongTree extends StripeWebHookBaseHandler
{
    private const PERCENT = 100;
    private const CERTIFICATE_GENERATED_IN_API = 'certificate generated in api';

    public function __construct(
        Stripe_invoices $invoices,
        private User_setting $userSetting,
        private Trees $trees,
        private Certificates $certificates,
        private Trees_on_first_sale $firstSale,
        private Trees_on_sale_pack $salePack,
        private Transactions $transactions,
        private Details_transactions $detailsTransactions,
        private Wallets $wallets,
        private Orders $orders,
        private Order_details $orderDetails,

    )
    {
        parent::__construct($invoices);
    }

    public function handle(Event $event): void {
        parent::handle($event);
        $this->updateInvoiceBySuccess();
        $invoice = $this->getInvoice();
        $user = UserInfoFacade::getUserInfo(id:$invoice['user_id']);
        $promoCode = !empty($invoice['promocode'])
            ? $this->getPromoCode($invoice['promocode'])
            : null;
        $locale = $invoice['lang'];
        $treeIds = json_decode($invoice['tree_ids'],true);
        $trees = $this->getTrees($treeIds);
        $groupArray = $this->getGroupArrayForTransactionDetail($treeIds);
        $salePackIds = $this->getSalePackIds($treeIds);
        $this->trees->getConnection()
            ->transaction(function () use (
                $invoice,
                $user,
                $treeIds,
                $promoCode,
                $groupArray,
                $trees,
                $salePackIds,
            ) {
                $transactionId = $this->insertInToTransactions(
                    $user,
                    $invoice,
                    $groupArray,
                    $trees
                );
                $this->updateTreesAndCertificates($user, $treeIds,$trees,$promoCode);
                $this->recalculateSalePack($salePackIds);
                if(!empty($promoCode)){
                    $this->workWithBonus($invoice,$promoCode,$transactionId,$user);
                }
            });
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
    private function getPromoCode(string $promoCode): ?array
    {
        return $this->userSetting
            ->where('promocode',$promoCode)
            ->select([
                'user_id',
                'promocode',
                'promocode_discount',
                'promocode_bonus',
                'promocode_wallet',
                'promocode_multiple',
                'promocode_area',
                'promocode_tree_min',
                'promocode_tree_max',
            ])
            ->first()
            ?->toArray();
    }
    private function getTrees(array $treeIds): array
    {
        return $this->firstSale
            ->from($this->firstSale->getTable(), 'fs')
            ->join($this->trees->getTable() . ' as t',
                'fs.tree_id',
                '=',
                't.id'
            )
            ->whereIn('tree_id',$treeIds)
            ->select([
                'fs.tree_id as id',
                'fs.price',
                't.planting_date',
            ])
            ->get()
            ->toArray();
    }
    private function getGroupArrayForTransactionDetail(array $treeIds): array
    {
        $sourceArray = $this->firstSale
            ->from($this->firstSale->getTable(), 'fs')
            ->join($this->salePack->getTable() . ' as sp',
                'fs.sale_pack_id',
                '=',
                'sp.id'
            )
            ->select([
                'fs.tree_id as id',
                'sp.user_id',
            ])
            ->whereIn('fs.tree_id',$treeIds)
            ->get()
            ->toArray();

        $groupedResult = [];

        // Цикл для обработки результатов запроса и группировки по sp.user_id
        foreach ($sourceArray as $row) {
            $userId = $row['user_id'];
            $treeId = $row['id'];
            // Если массив для данного sp.user_id еще не существует, создаем его
            if (!isset($groupedResult[$userId])) {
                $groupedResult[$userId] = [
                    'sp_user_id' => $userId,
                    'tree_ids' => []
                ];
            }
            // Добавляем tree_id в массив для текущего sp.user_id
            $groupedResult[$userId]['tree_ids'][] = $treeId;
        }
        $resultArray = [];
        foreach ($groupedResult as $item) {
            $resultArray[] = [
                'sp_user_id' => $item['sp_user_id'],
                'tree_ids' => '[' . implode(', ', $item['tree_ids']) . ']',
            ];
        }
        return $resultArray;
    }
    public function getSalePackIds(array $treeIds): array
    {
        return $this->firstSale
            ->whereIn('tree_id',$treeIds)
            ->distinct()
            ->pluck('sale_pack_id')
            ->toArray();
    }
    private function insertInToTransactions(
        array $user,
        array $invoice,
        array $groupArray,
        array $trees,
    ): int
    {
        $transactionId = $this->transactions
            ->insertGetId([
                'wallet_id' => $user['walletLivePayId'],
                'type' => TransactionTypes::BUY_YOUNG_TREE,
                'amount' => $invoice['amoute'],
                'commission' => 0,
                'total' => $invoice['amoute'],
                'tree_count' => count($trees),
                'data' => $invoice['tree_ids'],
                'status' => 1, //success in old transaction code
                'payment_service' => Payments::STRIPE['id'],
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => CurrencyName::USD['name'],
                'promocode' => $invoice['promocode'],
            ]);

        foreach ($groupArray as $item){
            $this->detailsTransactions
                ->insert([
                    'transaction_id' => $transactionId,
                    'from_user_id' => $item['sp_user_id'],
                    'to_user_id' => $user['id'],
                    'amount' => $invoice['amoute'],
                    'commission' => 0,
                    'total' => $invoice['amoute'],
                    'tree_count' => count($trees),
                    'data' => $item['tree_ids'],
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                ]);
        }

        $orderId = $this->orders
            ->insertGetId([
                'user_id' => $user['id'],
                'transaction_id' => $transactionId,
                'status' => 1,
                'trees_count' => count($trees),
                'total' => $invoice['amoute'],
                'signed_documents' => 0,
                'from_primary_market' => 0,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'uuid' => Uuid::generate()->string,
            ]);

        foreach ($trees as $tree){
            $this->orderDetails
                ->insert([
                    'order_id' => $orderId,
                    'tree_id' => $tree['id'],
                    'price' => $tree['price'],
                    'planting_date' => $tree['planting_date'],
                    'purchase_date' => CDateTime::getCurrentDate(),
                    'tree_order_status_id' => 2,
                    'gift_status_id' => 4,
                ]);
        }
        return $transactionId;
    }

    private function updateTreesAndCertificates(
        array $user,
        array $treesIds,
        array $trees,
        ?array $promoCode
    ): void
    {
        foreach ($trees as $tree) {
            $price = !empty($promoCode)
                ? intval($tree['price'] - ($tree['price'] / self::PERCENT) * doubleval($promoCode['promocode_discount']))
                : $tree['price'];
            $this->trees
                ->where('id', $tree['id'])
                ->update([
                    'user_id' => $user['id'],
                    'tree_sale_status_id' => TreeSaleStatus::ON_BALANCE,
                    'purchase_date' => CDateTime::getCurrentDate(),
                    'purchase_price' => $price,
                    'is_sold' => true,
                    'is_pending' => false,
                    'signed_documents' => false,
                    'tree_gift_status_id' => TreeGiftStatus::PURCHASED,
                    'updated_at' => CDateTime::getCurrentDate(),
                ]);
        }
        $this->firstSale
            ->whereIn('tree_id', $treesIds)
            ->delete();

        $certificatesData = [];
        foreach ($treesIds as $id) {
            $certificatesData[] = [
                'tree_id' => $id,
                'user_id' => $user['id'],
                'file' => self::CERTIFICATE_GENERATED_IN_API,
                'active' => true,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
            ];
        }
        $this->certificates
            ->insert($certificatesData);
    }
    private function recalculateSalePack(array $salePackIds): void
    {
        foreach ($salePackIds as $id){
            $count = $this->firstSale
                ->where('sale_pack_id',$id)
                ->count();

            $this->salePack
                ->where('id',$id)
                ->update([
                    'tree_count' => $count
                ]);
        }
    }
    private function workWithBonus(
        array $invoice,
        array $promoCode,
        int $transactionId,
        array $user
    ){
        if (!!$promoCode['promocode_bonus']){
            $promoOwner = UserInfoFacade::getUserInfo(id:$promoCode['user_id']);
            $replenishmentAmount = intval(
                intval($invoice['amoute']) / self::PERCENT * doubleval($promoCode['promocode_bonus'])
            );

            $this->detailsTransactions
                ->insert([
                    'transaction_id' => $transactionId,
                    'from_user_id' => $user['id'],
                    'to_user_id' => $promoCode['user_id'],
                    'amount' => $replenishmentAmount,
                    'commission' => 0,
                    'total' => $replenishmentAmount,
                    'tree_count' => 0,
                    'data' => json_encode([
                        'discount' => doubleval($promoCode['promocode_bonus']),
                        'code' => doubleval($promoCode['promocode']),
                    ]),
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                    'type' => 'promocode',
                ]);

            $this->wallets
                ->where('id',$promoOwner['walletBonusPayId'])
                ->update([
                    'balance' => DB::raw('balance + '.$replenishmentAmount)
                ]);
        }
    }
}
