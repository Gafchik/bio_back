<?php

namespace App\Http\Classes\LogicalModels\BuyYongTree;

class BuyYongTree
{
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
}
