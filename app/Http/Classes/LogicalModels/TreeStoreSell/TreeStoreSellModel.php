<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\FrozenToSaleException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\GiftException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\InvestorException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\LessCommissionException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\LessPriceException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\MoreCommissionException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\SignedDocumentsException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\TransactionException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\TreeOnSaleException;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\Roles;
use App\Http\Classes\Structure\TransactionStatus;
use App\Http\Classes\Structure\TransactionTypes;
use App\Http\Classes\Structure\TreeGiftStatus;
use App\Http\Classes\Structure\TreeSaleStatus;
use App\Models\MySql\Biodeposit\Details_transactions;
use App\Models\MySql\Biodeposit\Gifted_trees;
use App\Models\MySql\Biodeposit\RoleUsers;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_sale;
use App\Models\MySql\Biodeposit\Variables;
use App\Models\MySql\Biodeposit\Wallets;
use App\Models\MySql\Biodeposit\Trees_on_first_sale;
use Illuminate\Support\Facades\Auth;

class TreeStoreSellModel
{
    public function __construct(
        private Trees_on_sale $treesOnSale,
        private Trees $trees,
        private RoleUsers $roleUsers,
        private Transactions $transactions,
        private Details_transactions $detailsTransactions,
        private Wallets $wallets,
        private Gifted_trees $gifted_trees,
    ){}
    public function getWallet(): array
    {
        return $this->wallets
            ->where('user_id',Auth::user()->id)
            ->whereNull('type')
            ->first()
            ->toArray();
    }
    public function getPosition(int $commission): int
    {
        return $this->treesOnSale
            ->where('commission', '>=', $commission)
                ->orderBy('commission', 'asc')
                ->orderBy('created_at', 'desc')
                ->count() + 1;
    }
    public function getCurrenTrees(array $ids): array
    {
        return $this->trees
            ->from($this->trees->getTable(), 'trees')
            ->leftJoin($this->gifted_trees->getTable() . ' as gifted_trees',
                'gifted_trees.tree_id',
                '=',
                'trees.id',
            )
            ->select([
                'trees.id',
                'trees.uuid',
                'trees.user_id',
                'trees.tree_type_id',
                'trees.is_virtual',
                'trees.planting_date',
                'trees.season',
                'trees.is_young',
                'trees.purchase_date',
                'trees.purchase_price',
                'trees.field_id',
                'trees.coordinates',
                'trees.utm_coordinates',
                'trees.is_sold',
                'trees.is_gifted',
                'trees.is_pending',
                'trees.tree_sale_status_id',
                'trees.tree_gift_status_id',
                'trees.sale_frozen_to',
                'trees.dividend_frozen_to',
                'trees.initial_price',
                'trees.current_price',
                'trees.signed_documents',
                'trees.currency_id',
                'gifted_trees.tree_gift_status_id',
            ])
            ->whereIn('trees.id', $ids)
            ->get()
            ->toArray();
    }
    public function getRoles(): array
    {
        return $this->roleUsers
            ->where('user_id',Auth::user()->id)
            ->pluck('role_id')
            ->toArray();
    }
    public function sell(array $trees, array $roles, array $treeIds): void
    {
        $user = Auth::user();
        $wallet = $this->getWallet();
        $moneyInfo = $this->getMoneyInfo($trees);
        $this->checkTrees($trees,$roles);
        $this->trees->getConnection()->beginTransaction();
        try {

            foreach ($trees as $tree) {
                $this->insertToTreeToSaleList($tree);
                $this->trees->where('id', $tree['id'])
                    ->update([
                        'tree_sale_status_id' => TreeSaleStatus::ON_SALE
                    ]);
            }
            $transactionId = $this->transactions->insertGetId([
                'wallet_id' => $wallet['id'],
                'type' => TransactionTypes::SELL,
                'amount' => $moneyInfo['amount'],
                'commission' => $moneyInfo['commission'],
                'total' => $moneyInfo['total'],
                'tree_count' => count($trees),
                'data' => json_encode($treeIds),

                'status' => TransactionStatus::EXHIBITED,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => 'USD',
            ]);
            $this->detailsTransactions->insert([
                'transaction_id' => $transactionId,
                'from_user_id' => $user->id,
                'amount'       => $moneyInfo['amount'],
                'commission'   => $moneyInfo['commission'],
                'total'        => $moneyInfo['total'],
                'data'         => json_encode($treeIds),
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
            ]);
            $this->trees->getConnection()->commit();
        }catch (\Throwable $exception){
            $this->trees->getConnection()->rollBack();
            throw new TransactionException($exception->getMessage());
        }
    }
    private function insertToTreeToSaleList(array $tree): void
    {
        $tree_on_sale_last = Trees_on_sale::where('commission', '>=', $tree['commission'])
            ->orderBy('commission', 'asc')
            ->orderBy('created_at', 'desc')
            ->first();
        $tree_on_sale = new Trees_on_sale([
            'tree_id' => $tree['id'],
            'price' => $tree['sell_amount']*100,
            'commission' => $tree['commission'],
            'created_at' => CDateTime::getCurrentDate(),
            'updated_at' => CDateTime::getCurrentDate(),
        ]);
        if ($tree_on_sale_last) {
            $tree_on_sale->insertAfterNode($tree_on_sale_last);
        } else {
            $first_tree_on_sale = Trees_on_sale::defaultOrder()->first();
            if ($first_tree_on_sale) {
                $tree_on_sale->insertBeforeNode($first_tree_on_sale);
            } else {
                $tree_on_sale->save();
            }
        }
    }
    private function checkTrees(array $trees,array $roles):void
    {
        foreach ($trees as $tree){
            if(!!$tree['is_young'] && in_array(Roles::INVESTORS,$roles)){
                throw new  InvestorException($tree['uuid']);
            }
            $treeInShop = Trees_on_sale::where('tree_id', $tree['id'])->exists();
            $treeInFirsSale = Trees_on_first_sale::where('tree_id', $tree['id'])->exists();
            if ($treeInShop || $treeInFirsSale) {
                throw new TreeOnSaleException($tree['uuid']);
            }
            if($tree['tree_gift_status_id'] === TreeGiftStatus::CHARITY){
                throw new GiftException($tree['uuid']);
            }
            if(!empty($tree['sale_frozen_to'])){
                if(CDateTime::isDateEarlier(CDateTime::getCurrentDate(),$tree['sale_frozen_to'])){
                    throw new FrozenToSaleException($tree['uuid'],$tree['sale_frozen_to']);
                }
            }
            if(!$tree['signed_documents']){
                throw new SignedDocumentsException($tree['uuid']);
            }
            if(!!$tree['is_gifted']){
                throw new GiftException($tree['uuid']);
            }
            if(($tree['sell_amount']*100)<$tree['current_price']){
                throw new LessPriceException($tree['uuid']);
            }
            if($tree['commission']>99){
                throw new MoreCommissionException($tree['uuid']);
            }
            if ($tree['commission'] < Variables::getValueByKey('shop_commission')) {
                throw new LessCommissionException($tree['uuid']);
            }
        }
    }
    private function getMoneyInfo(array $trees): array
    {
        $result = [
            'amount' => 0,
            'commission' => 0,
            'total' => 0,
        ];
        foreach ($trees as $tree){
            $result['amount'] += ($tree['sell_amount'] *100);
            $result['commission'] += ($tree['sell_amount'] *100)*$tree['commission']/100;
            $result['total'] += $result['amount'] + $result['commission'];
        }
        return $result;
    }
    public function getTreeInSell(): array
    {
        return $this->trees
            ->from($this->trees->getTable(), 'trees')
            ->join($this->treesOnSale->getTable() . ' as treesOnSale',
                'treesOnSale.tree_id',
                '=',
                'trees.id',
            )
            ->where('trees.user_id',Auth::user()->id)
            ->select([
                'trees.id',
                'trees.uuid',
                'trees.user_id',
                'trees.tree_type_id',
                'trees.is_virtual',
                'trees.planting_date',
                'trees.season',
                'trees.is_young',
                'trees.purchase_date',
                'trees.purchase_price',
                'trees.field_id',
                'trees.coordinates',
                'trees.utm_coordinates',
                'trees.is_sold',
                'trees.is_gifted',
                'trees.is_pending',
                'trees.tree_sale_status_id',
                'trees.tree_gift_status_id',
                'trees.sale_frozen_to',
                'trees.dividend_frozen_to',
                'trees.initial_price',
                'trees.current_price',
                'trees.signed_documents',
                'trees.currency_id',
                'treesOnSale.price',
                'treesOnSale.commission',
            ])
            ->get()
            ->toArray();
    }
    public function removeSell(array $treeInSell,array $treeIds): void
    {
        $user = Auth::user();
        $wallet = $this->getWallet();
        $moneyInfo = $this->getMoneyInfoRemove($treeInSell);
        $this->trees->getConnection()->beginTransaction();
        try {
            foreach ($treeInSell as $tree) {
                $this->treesOnSale
                    ->where('tree_id', $tree['id'])
                    ->delete();
                $this->trees->where('id', $tree['id'])
                    ->update([
                        'tree_sale_status_id' => TreeSaleStatus::ON_BALANCE
                    ]);
            }
            $transactionId = $this->transactions->insertGetId([
                'wallet_id' => $wallet['id'],
                'type' => TransactionTypes::REMOVE_FROM_SELL,
                'amount' => $moneyInfo['amount'],
                'commission' => $moneyInfo['commission'],
                'total' => $moneyInfo['total'],
                'tree_count' => count($treeInSell),
                'data' => json_encode($treeIds),

                'status' => TransactionStatus::WITHDRAWN,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => 'USD',
            ]);
            $this->detailsTransactions->insert([
                'transaction_id' => $transactionId,
                'from_user_id' => $user->id,
                'amount'       => $moneyInfo['amount'],
                'commission'   => $moneyInfo['commission'],
                'total'        => $moneyInfo['total'],
                'data'         => json_encode($treeIds),
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
            ]);
            $this->trees->getConnection()->commit();
        }catch (\Throwable $exception){
            $this->trees->getConnection()->rollBack();
            throw new TransactionException($exception->getMessage());
        }
    }
    private function getMoneyInfoRemove(array $trees): array
    {
        $result = [
            'amount' => 0,
            'commission' => 0,
            'total' => 0,
        ];
        foreach ($trees as $tree){
            $result['amount'] += $tree['price'];
            $result['commission'] += $tree['price']*$tree['commission']/100;
            $result['total'] += $result['amount'] + $result['commission'];
        }
        return $result;
    }
}
