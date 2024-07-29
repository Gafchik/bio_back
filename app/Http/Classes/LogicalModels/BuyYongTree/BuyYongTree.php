<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree;

use App\Http\Classes\LogicalModels\BuyYongTree\Exceptions\MaxTreesExceptionStripe;
use App\Http\Facades\AvailableTreeFacade;
use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Facades\WorkWithPromoFacade;

class BuyYongTree
{
    private const MAX_TREES = 100;
    public function __construct(
        private BuyYongTreeModel $model
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
        throw_if(
            $data['countTree'] > self::MAX_TREES,
            new MaxTreesExceptionStripe(self::MAX_TREES)
        );
        $availableTree = AvailableTreeFacade::getAvailableYoungOliveTrees($data['countTree']);
        $promoCode = !empty($data['promo'])
            ? WorkWithPromoFacade::workWithPromo($data['promo'],count($availableTree))
            : null;
        $this->model->buyBalance($availableTree,$promoCode);
    }

}
