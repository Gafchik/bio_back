<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree;

use App\Models\MySql\Biodeposit\Trees_on_sale_pack;
use App\Models\MySql\Biodeposit\Variables;

class BuyYongTreeModel
{
    public function __construct(
        private Trees_on_sale_pack $treesOnSalePack,
    ){}
    public function getCountTreeInSell(): int
    {
        return $this->treesOnSalePack->sum('tree_count');

    }
    public function getPriceYongTree(): int
    {
        return Variables::getValueByKey('tree_price_first_sale');
    }
}
