<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo;

interface WorkWithPromoInterface
{
    public function workWithPromo(string $promo, int $countTrees): array;
}
