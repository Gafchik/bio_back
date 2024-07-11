<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell;

use App\Exceptions\BaseExceptions\BaseException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\GiftException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\InvestorException;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\TreeOnSaleException;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\Roles;
use App\Http\Classes\Structure\TransactionStatus;
use App\Http\Classes\Structure\TransactionTypes;
use App\Http\Classes\Structure\TreeGiftStatus;
use App\Models\MySql\Biodeposit\Gifted_trees;
use App\Models\MySql\Biodeposit\RoleUsers;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_sale;
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

//        $this->trees->getConnection()->beginTransaction();
//        $transactionId = $this->transactions->insertGetId([
//            'wallet_id' => $wallet['id'],
//            'type' => TransactionTypes::SELL,
//            'amount' => $moneyInfo['amount'],
//            'commission' => $moneyInfo['commission'],
//            'total' => $moneyInfo['total'],
//            'tree_count' => count($trees),
//            'data' => json_encode($treeIds),
//
//            'status' => TransactionStatus::EXHIBITED,
//            'created_at' => CDateTime::getCurrentDate(),
//            'updated_at' => CDateTime::getCurrentDate(),
//            'exchange_currency' => 'USD',
//        ]);
        try {
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
                //TODO проверка на заморозку
                dd($tree['sale_frozen_to']);
                dd($tree);

            }
//            $this->trees->getConnection()->commit();
        }catch (BaseException $exception){
//            $this->trees->getConnection()->rollBack();
            throw $exception;
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
}
