<?php

namespace App\Http\Classes\LogicalModels\Store;

use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreModel
{
    public function __construct(
        private Trees_on_sale $treesOnSale,
        private Trees $trees,
    ){}

    public function getTreeStore(): array
    {
        $userId = Auth::user()?->id;
        $query = $this->treesOnSale
            ->from($this->treesOnSale->getTable(), 'treesOnSale')
            ->leftJoin($this->trees->getTable() . ' as trees',
                'treesOnSale.tree_id',
                '=',
                'trees.id',
            )
            ->selectRaw('count(*) as count')
            ->selectRaw('YEAR(trees.planting_date) as year')
            ->groupByRaw('YEAR(trees.planting_date)')
            ->orderByRaw('YEAR(trees.planting_date)')
            ->where('trees.is_pending', false);
        if(!is_null($userId)){
            $query->where('trees.user_id','!=', $userId);
        }
        return $query
            ->get()
            ->toArray();
    }
    public function getTreeByYear(array $data): array
    {
        $userId = Auth::user()?->id;
        DB::statement("SET @rank = 0");

        $query = DB::table('trees_on_sale')
            ->leftJoin('trees', 'trees_on_sale.tree_id', '=', 'trees.id')
            ->select([
                'trees.season',
                'trees_on_sale.price',
                'trees_on_sale.tree_id',
                DB::raw('YEAR(trees.planting_date) AS year'),
                DB::raw('TIMESTAMPDIFF(YEAR, trees.planting_date, CURDATE()) AS age'),
                DB::raw('@rank := @rank + 1 AS position')
            ])
            ->whereRaw('YEAR(trees.planting_date) = ?', [$data['year']])
            ->where('trees.is_pending', false);
            if(!is_null($userId)){
                $query->where('trees.user_id','!=', $userId);
            }
            return $query
                ->orderByDesc('trees_on_sale.commission')
                ->orderBy('trees_on_sale.created_at')
                ->get()
                ->toArray();
    }
}

