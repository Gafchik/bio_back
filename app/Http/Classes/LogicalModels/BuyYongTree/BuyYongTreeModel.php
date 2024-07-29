<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_sale_pack;
use App\Models\MySql\Biodeposit\Variables;

class BuyYongTreeModel
{
    public function __construct(
        private Trees_on_sale_pack $treesOnSalePack,
        private Trees $trees,
    ){}
    public function getCountTreeInSell(): int
    {
        return $this->treesOnSalePack->sum('tree_count');

    }
    public function getPriceYongTree(): int
    {
        return Variables::getValueByKey('tree_price_first_sale');
    }
    public function buyBalance(array $trees, ?array $promoCode): void
    {
        $treeIds = TransformArrayHelper::getArrayUniqueByField(
            $trees, 'id'
        );
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
}
