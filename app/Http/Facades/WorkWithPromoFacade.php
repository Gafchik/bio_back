<?php

namespace App\Http\Facades;

use App\Http\Classes\LogicalModels\Common\WorkWithPromo\WorkWithPromoInterface;
use Illuminate\Support\Facades\Facade;
/**
 * @method static array workWithPromo(string $promo, int $countTrees);
 * @see WorkWithPromoInterface
 */
class WorkWithPromoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'work_with_promo_facade';
    }
}
