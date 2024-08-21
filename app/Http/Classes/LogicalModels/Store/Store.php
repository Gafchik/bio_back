<?php

namespace App\Http\Classes\LogicalModels\Store;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Classes\LogicalModels\Store\Exceptions\NoMoneyException;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\TransactionTypes;
use App\Http\Facades\UserInfoFacade;
use Illuminate\Support\Facades\Auth;

class Store
{
    public function __construct(
        private StoreModel $model
    ){}
    public function getTreeStore(): array
    {
        return $this->model->getTreeStore();
    }
    public function getTreeByYear(array $data): array
    {
        return $this->model->getTreeByYear($data);
    }
    public function buyFromBasket(array $data): array
    {
        $user = UserInfoFacade::getUserInfo(id:Auth::user()?->id);
        $treeIds = array_column($data['basket'], 'tree_id');
        $treesInStore = $this->model->getTreeStoreByIds($treeIds);
        $treesInStoreIds = array_column($treesInStore, 'tree_id');
        $pendingTrees = TransformArrayHelper::callbackSearchAllInArray(
            $treeIds,
            fn ($id) => !in_array($id,$treesInStoreIds)
        );
        if(!empty($pendingTrees)){
            return [
                'errors' => true,
                'trees' => $pendingTrees
            ];
        }
        $transactionData = $this->prepareTransactionData($treesInStore,$user);
        if($user['wallet_live_pay_balance'] > $transactionData['total']){
            throw new NoMoneyException();
        }
        $this->model->buyFromBasket($treesInStore,$transactionData,$user);
        return [
            'errors' => false,
        ];
    }
    private function prepareTransactionData(array $treesInStore, array $user): array
    {
        $result = [
            'total' => 0,
            'commission' => 0,
            'amount' => 0,
            'type' => TransactionTypes::BUY_YOUNG_TREE,
            'wallet_id' => $user['wallet_live_pay_id'],
            'data' => json_encode(array_column($treesInStore, 'tree_id')),
            'tree_count' => count($treesInStore),
            'status' => 1,
            'created_at' => CDateTime::getCurrentDate(),
            'updated_at' => CDateTime::getCurrentDate(),
        ];
        foreach ($treesInStore as $tree) {
            $result['total'] += $tree->price;
            $commission = $tree->price /100 * (int)$tree->commission_percent ;
            $amount = $tree->price - $commission;
            $result['commission'] += $commission;
            $result['amount'] += $amount;
        }
        return $result;
    }
}
