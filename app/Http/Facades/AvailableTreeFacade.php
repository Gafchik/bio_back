<?php

namespace App\Http\Facades;

use App\Exceptions\BaseExceptions\BaseException;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAvailableYoungOliveTrees(int $countTrees);
 * @see AvailableTreeInterface
 */
class AvailableTreeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'available_tree_facade';
    }
}
