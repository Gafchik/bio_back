<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo;

use App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions\CountTreesException;
use App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions\PromoActionException;
use App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions\PromoCodeNotFoundException;
use App\Http\Classes\LogicalModels\Common\WorkWithPromo\Exceptions\UsedPromoException;
use App\Http\Classes\Structure\PromoCodeActionsType;

class WorkWithPromo implements WorkWithPromoInterface
{
    public function __construct(
        public WorkWithPromoModel $model
    ){}

    public function workWithPromo(string $promo, int $countTrees): array
    {
        $promoCode = $this->model->getPromoCode($promo);
        if(empty($promoCode)) {
            throw new PromoCodeNotFoundException();
        }
        $this->checkPromoCode($promoCode, $countTrees);
        return $promoCode;
    }
    private function checkPromoCode(array $promoCode, int $countTrees): void
    {
        $this->checkPromoCount($promoCode, $countTrees);
        $this->checkPromoAction($promoCode);
        if (!$promoCode['promocode_multiple']) {
            $this->checkPromoUsing($promoCode);
        }
    }
    private function checkPromoCount(array $promoCode, int $countTrees): void
    {
        $min = $promoCode['promocode_tree_min'];
        $max = $promoCode['promocode_tree_max'];

        if($min !== null && $min !== 0){
            if($min > $countTrees){
                throw new CountTreesException();
            }
        }
        if($max !== null && $max !== 0){
            if($max < $countTrees){
                throw new CountTreesException();
            }
        }
    }
    private function checkPromoAction(array $promoCode): void
    {
        $actionsArray = !empty($promoCode['promocode_area'])
            ? json_decode($promoCode['promocode_area'], true)
            : [];
        if (!in_array(PromoCodeActionsType::FIRSTSALE_BUY, $actionsArray)) {
            throw new PromoActionException();
        }
    }
    private function checkPromoUsing(array $promoCode): void
    {
        if ($this->model->checkUsingInTransaction($promoCode)) {
            throw new UsedPromoException();
        }
    }
}
