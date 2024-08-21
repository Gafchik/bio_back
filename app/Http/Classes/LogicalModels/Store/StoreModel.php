<?php

namespace App\Http\Classes\LogicalModels\Store;

use App\Http\Classes\LogicalModels\Store\Exceptions\ErrorTransactionBuyShop;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\TransactionTypes;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_sale;
use App\Models\MySql\Biodeposit\Wallets;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Details_transactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreModel
{
    public function __construct(
        private Trees_on_sale $treesOnSale,
        private Trees $trees,
        private Wallets $wallets,
        private Transactions $transactions,
        private Details_transactions $detailsTransactions,
    ){}

    public function getTreeStore(): array
    {
        $userId = Auth::user()?->id;
        $query = $this->treesOnSale
            ->from($this->treesOnSale->getTable(), 'treesOnSale')
            ->leftJoin($this->trees->getTable() . ' as trees',
                'treesOnSale.tree_id',
                '=',
                'trees.id',
            )
            ->selectRaw('count(*) as count')
            ->selectRaw('YEAR(trees.planting_date) as year')
            ->groupByRaw('YEAR(trees.planting_date)')
            ->orderByRaw('YEAR(trees.planting_date)')
            ->where('trees.is_pending', false);
        if(!is_null($userId)){
            $query->where('trees.user_id','!=', $userId);
        }
        return $query
            ->get()
            ->toArray();
    }
    public function getTreeByYear(array $data): array
    {
        $userId = Auth::user()?->id;
        DB::statement("SET @rank = 0");

        $query = DB::table('trees_on_sale')
            ->leftJoin('trees', 'trees_on_sale.tree_id', '=', 'trees.id')
            ->select([
                'trees.season',
                'trees_on_sale.price',
                'trees_on_sale.tree_id',
                DB::raw('YEAR(trees.planting_date) AS year'),
                DB::raw('TIMESTAMPDIFF(YEAR, trees.planting_date, CURDATE()) AS age'),
                DB::raw('@rank := @rank + 1 AS position')
            ])
            ->whereRaw('YEAR(trees.planting_date) = ?', [$data['year']])
            ->where('trees.is_pending', false)
            ->where('trees_on_sale.is_pending', false);
            if(!is_null($userId)){
                $query->where('trees.user_id','!=', $userId);
            }
            return $query
                ->orderByDesc('trees_on_sale.commission')
                ->orderBy('trees_on_sale.created_at')
                ->get()
                ->toArray();
    }
    public function getTreeStoreByIds(array $ids): array
    {
        $userId = Auth::user()?->id;
        DB::statement("SET @rank = 0");

        $query = DB::table('trees_on_sale')
            ->leftJoin('trees', 'trees_on_sale.tree_id', '=', 'trees.id')
            ->select([
                'trees.user_id as owner_id',
                'trees.season',
                'trees_on_sale.price',
                'trees_on_sale.tree_id',
                'trees_on_sale.commission as commission_percent',
                DB::raw('YEAR(trees.planting_date) AS year'),
                DB::raw('TIMESTAMPDIFF(YEAR, trees.planting_date, CURDATE()) AS age'),
                DB::raw('@rank := @rank + 1 AS position')
            ])
            ->whereIn('trees_on_sale.tree_id', $ids)
            ->where('trees.is_pending', false)
            ->where('trees_on_sale.is_pending', false);
        if(!is_null($userId)){
            $query->where('trees.user_id','!=', $userId);
        }
        return $query
            ->orderByDesc('trees_on_sale.commission')
            ->orderBy('trees_on_sale.created_at')
            ->get()
            ->toArray();
    }
    public function buyFromBasket(array $trees, array $transactionData, array $user): void
    {
        //TODO узнать куда идет коммисия
        $this->trees->getConnection()->beginTransaction();
        try {
            //списуем с баланса у покупателя
            $this->wallets
                ->where('id', $user['wallet_live_pay_id'])
                ->update([
                    'balance' => $user['wallet_live_pay_balance'] - $transactionData['total'],
                ]);
            //создаем транзакцию
            $transactionId = $this->transactions->insertGetId($transactionData);
            foreach ($trees as $tree)
            {
                $commission = $tree->price /100 * (int)$tree->commission_percent;
                $amount = $tree->price - $commission;
                //детали транзакции
                $this->detailsTransactions->insert([
                    'transaction_id' => $transactionId,
                    'from_user_id' => $user['id'],
                    'to_user_id' => $tree->owner_id,
                    'amount' => $amount,
                    'commission' => $commission,
                    'total' => $tree->price,
                    'tree_count' => 1,
                    'data' => '['.$tree->tree_id.']',
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                ]);
                //меняем владельца дерева
                $this->trees->where('id', $tree->tree_id)
                    ->update([
                        'user_id' => $user['id'],
                        'is_sold' => 1,
                        'purchase_date' => CDateTime::getCurrentDate(),
                        'purchase_price' => $tree->price,
                        'updated_at' => CDateTime::getCurrentDate(),
                    ]);
                //начисляем деньги продавцу
                $this->workWithOwnerTree($tree,$amount,$commission);
            }
            $this->trees->getConnection()->commit();
        }catch (\Throwable $e){
            $this->trees->getConnection()->rollBack();
            throw new ErrorTransactionBuyShop();
        }
    }
    private function getWallet(int $id): ?array
    {
        return $this->wallets
            ->where('user_id', $id)
            ->whereNull('type')
            ->first([
                'id',
                'balance'
            ])?->toArray();
    }
    private function workWithOwnerTree(
        $tree,
        int $amount,
        int $commission
    ): void
    {
        $ownerWallet = $this->getWallet($tree->owner_id);
        $this->wallets
            ->where('id', $ownerWallet['id'])
            ->update([
                'balance' => $ownerWallet['balance'] + $amount,
            ]);
        $this->transactions->insert([
            'amount' => $amount,
            'commission' => $commission,
            'total' => $tree->price,
            'type' => TransactionTypes::SELL,
            'wallet_id' => $ownerWallet['id'],
            'data' => '['.$tree->tree_id.']',
            'tree_count' => 1,
            'status' => 1,
            'created_at' => CDateTime::getCurrentDate(),
            'updated_at' => CDateTime::getCurrentDate(),
        ]);
    }
}

