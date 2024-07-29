<?php

namespace App\Http\Classes\LogicalModels\Common\AvailableTree;

use App\Http\Classes\LogicalModels\Common\AvailableTree\Exceptions\NotAvailableExceptionsGetAvailableYoungOliveTrees;

class AvailableTree implements AvailableTreeInterface
{
    public function __construct(
        private AvailableTreeModel $model
    ){}

    public function getAvailableYoungOliveTrees(int $countTrees): array
    {
        $investorsIds = $this->model->getInvestorsIds();
        $availableCountTrees = $this->model->getCountAvailableTrees($investorsIds);
        if($countTrees > $availableCountTrees){
            throw new NotAvailableExceptionsGetAvailableYoungOliveTrees($availableCountTrees);
        }
        return $this->model->getAvailableTrees($countTrees,$investorsIds);
    }
}
