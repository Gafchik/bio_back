<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\CurrencyName;
use App\Http\Classes\Structure\InvoiceType;
use App\Http\Classes\Structure\Payments;
use App\Http\Classes\Structure\StripeInvoiceStatuses;
use App\Http\Classes\Structure\TransactionTypes;
use App\Http\Classes\Structure\TreeGiftStatus;
use App\Http\Classes\Structure\TreeSaleStatus;
use App\Http\Classes\Structure\WalletsType;
use App\Http\DTO\Core\UserInfo\UserInfoDto;
use App\Http\Facades\UserInfoFacade;
use App\Models\MySql\Biodeposit\Certificates;
use App\Models\MySql\Biodeposit\Details_transactions;
use App\Models\MySql\Biodeposit\Order_details;
use App\Models\MySql\Biodeposit\Orders;
use App\Models\MySql\Biodeposit\Stripe_invoices;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_first_sale;
use App\Models\MySql\Biodeposit\Trees_on_sale_pack;
use App\Models\MySql\Biodeposit\User_setting;
use App\Models\MySql\Biodeposit\Variables;
use App\Models\MySql\Biodeposit\Wallets;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class BuyYongTreeModel
{
    private const CERTIFICATE_GENERATED_IN_API = 'certificate generated in api';
    public function __construct(
        private Trees_on_sale_pack $treesOnSalePack,
        private Trees $trees,
        private Trees_on_first_sale $firstSale,
        private Trees_on_sale_pack $salePack,
        private User_setting $userSetting,
        private Certificates $certificates,
        private Transactions $transactions,
        private Details_transactions $detailsTransactions,
        private Wallets $wallets,
        private Orders $orders,
        private Order_details $orderDetails,
        private Stripe_invoices $invoices,

    ){}
    public function getCountTreeInSell(): int
    {
        return $this->treesOnSalePack->sum('tree_count');

    }
    public function getPriceYongTree(): int
    {
        return Variables::getValueByKey('tree_price_first_sale');
    }
    public function buyBalance(array $allData): void
    {
        $this->trees->getConnection()
            ->transaction(function () use ($allData) {
                $transactionId = $this->insertInToTransactions($allData);
                $allData['transaction_id'] = $transactionId;
                $this->updateTreesAndCertificates($allData);
                $this->takeOffBalance($allData);
                $this->recalculateSalePack($allData);
                if(!empty($promoCode)){
                    $this->workWithBonus($allData);
                }
            });
    }
    public function getGroupArrayForTransactionDetail(array $treeIds): array
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
    private function insertInToTransactions(array $allData): int
    {
        $walletLivePayId = TransformArrayHelper::callbackSearchFirstInArray(
            $allData['wallets'],
            fn($w) => is_null($w['type'])
        );
        $transactionId = $this->transactions
            ->insertGetId([
                'wallet_id' => $walletLivePayId['id'],
                'type' => TransactionTypes::BUY_YOUNG_TREE,
                'amount' => $allData['price'],
                'commission' => 0,
                'total' => $allData['price'],
                'tree_count' => count($allData['treeIds']),
                'data' => '['.implode(',',$allData['treeIds']).']',
                'status' => 1, //success in old transaction code
                'payment_service' => Payments::BALANCE['id'],
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => CurrencyName::USD['name'],
                'promocode' => $allData['promoCode']['promocode'] ?? null,
            ]);
        foreach ($allData['groupArray'] as $item){
            $this->detailsTransactions
                ->insert([
                    'transaction_id' => $transactionId,
                    'from_user_id' => $item['sp_user_id'],
                    'to_user_id' => $allData['user']['id'],
                    'amount' => $allData['price'],
                    'commission' => 0,
                    'total' => $allData['price'],
                    'tree_count' => count($allData['treeIds']),
                    'data' => $item['tree_ids'],
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                ]);
        }
        $orderId = $this->orders
            ->insertGetId([
                'user_id' => $allData['user']['id'],
                'transaction_id' => $transactionId,
                'status' => 1,
                'trees_count' => count($allData['treeIds']),
                'total' => $allData['price'],
                'signed_documents' => 0,
                'from_primary_market' => 0,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'uuid' => Uuid::generate()->string,
            ]);
        foreach ($allData['availableTree'] as $tree){
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
    public function getWallets(int $id): array
    {
        return $this->wallets
            ->where('user_id', $id)
            ->get([
                'id',
                'type',
                'balance',
            ])
            ->toArray();
    }
    private function updateTreesAndCertificates($allData): void
    {
        foreach ($allData['availableTree'] as $tree) {
            $this->trees
                ->where('id', $tree['id'])
                ->update([
                    'user_id' => $allData['user']['id'],
                    'tree_sale_status_id' => TreeSaleStatus::ON_BALANCE,
                    'purchase_date' => CDateTime::getCurrentDate(),
                    'purchase_price' => $allData['price'],
                    'is_sold' => true,
                    'is_pending' => false,
                    'signed_documents' => false,
                    'tree_gift_status_id' => TreeGiftStatus::PURCHASED,
                    'updated_at' => CDateTime::getCurrentDate(),
                ]);
        }
        $this->firstSale
            ->whereIn('tree_id', $allData['treeIds'])
            ->delete();

        $certificatesData = [];
        foreach ($allData['treeIds'] as $id) {
            $certificatesData[] = [
                'tree_id' => $id,
                'user_id' => $allData['user']['id'],
                'file' => self::CERTIFICATE_GENERATED_IN_API,
                'active' => true,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
            ];
        }
        $this->certificates
            ->insert($certificatesData);
    }
    private function recalculateSalePack(array $allData): void
    {
        foreach ($allData['salePackIds'] as $id){
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
    public function getSalePackIds(array $treeIds): array
    {
        return $this->firstSale
            ->whereIn('tree_id',$treeIds)
            ->distinct()
            ->pluck('sale_pack_id')
            ->toArray();
    }
    private function takeOffBalance(array $allData): void
    {
        $this->wallets
            ->where('user_id', $allData['user']['id'])
            ->where('type', WalletsType::LIVE_PAY)
            ->update([
                'balance' => DB::raw('balance - ' . $allData['price'])
            ]);
    }
    private function workWithBonus(array $allData): void
    {
        if (!!$allData['promoCode']['promocode']) {

            $replenishmentAmount = intval(
                intval($allData['price']) / 100 * doubleval($allData['promoCode']['promocode_bonus'])
            );

            $this->detailsTransactions
                ->insert([
                    'transaction_id' => $allData['transaction_id'],
                    'from_user_id' => $allData['user']['id'],
                    'to_user_id' => $allData['promoCode']['user_id'],
                    'amount' => $replenishmentAmount,
                    'commission' => 0,
                    'total' => $replenishmentAmount,
                    'tree_count' => 0,
                    'data' => json_encode([
                        'discount' => doubleval($allData['promoCode']['promocode_bonus']),
                        'code' => doubleval($allData['promoCode']['promocode']),
                    ]),
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                    'type' => 'promocode',
                ]);

            $this->wallets
                ->where('user_id', $allData['promoCode']['user_id'])
                ->where('type', WalletsType::BONUS)
                ->update([
                    'balance' => DB::raw('balance + ' . $replenishmentAmount)
                ]);
        }
    }
    public function setPendingStatus(array $availableTreeIds,bool $isPending = true): void
    {
        $this->trees->whereIn('id',$availableTreeIds)
            ->update([
                'is_pending' => $isPending
            ]);
        $this->firstSale->whereIn('tree_id',$availableTreeIds)
            ->update([
                'is_pending' => $isPending,
                'updated_at' => CDateTime::getCurrentDate()
            ]);
    }
    public function createEmptyInvoiceYongTree(array $treeIds,int $userId, ?array $promoCode): int
    {
        $insertDate = [
            'status_id' => StripeInvoiceStatuses::CREATED['id'],
            'user_id' => $userId,
            'invoice_type' => InvoiceType::BUY_YOUNG_OLIVE_TREE,
            'tree_ids' => '['.implode(',',$treeIds).']',
            'lang' => app()->getLocale(),
            'ccy_id' => CurrencyName::USD['id'],
            'create_date' => CDateTime::getCurrentDate(),
            'modified_date' => CDateTime::getCurrentDate(),
        ];
        if(!empty($promoCode)){
            $insertDate['promocode'] = $promoCode['promocode'];
        }
        return $this->invoices
            ->insertGetId($insertDate);
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
