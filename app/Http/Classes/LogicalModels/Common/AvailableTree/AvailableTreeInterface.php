<?php

namespace App\Http\Classes\LogicalModels\Common\AvailableTree;

interface AvailableTreeInterface
{
    public function getAvailableYoungOliveTrees(int $countTrees): array;
}
