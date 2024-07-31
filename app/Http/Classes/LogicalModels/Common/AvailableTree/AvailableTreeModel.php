<?php

namespace App\Http\Classes\LogicalModels\Common\AvailableTree;

use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\Roles;
use App\Http\Classes\Structure\TreeSaleStatus;
use App\Http\Classes\Structure\TreesSalePackType;
use App\Models\MySql\Biodeposit\RoleUsers;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Trees_on_first_sale;
use App\Models\MySql\Biodeposit\Trees_on_sale_pack;
use App\Models\MySql\Biodeposit\Users;
use Illuminate\Database\Eloquent\Builder;

class AvailableTreeModel
{
    public function __construct(
        private Users $users,
        private Trees $trees,
        private Trees_on_first_sale $treesOnFirstSale,
        private RoleUsers $roleUsers,
        private Trees_on_sale_pack $salePack,
    ){}

    private function getBaseQuery(array $investorsIds): Builder
    {
        return $this->trees
            ->from($this->trees->getTable(), 'tree')
            ->join($this->treesOnFirstSale->getTable() . ' as first_sale',
                'tree.id',
                '=',
                'first_sale.tree_id'
            )
            ->join($this->salePack->getTable() . ' as sale_pack',
                'sale_pack.id',
                '=',
                'first_sale.sale_pack_id'
            )
            ->where('sale_pack.type',TreesSalePackType::EXPRESS_TREE_PACK)
            ->where('tree.tree_sale_status_id',TreeSaleStatus::ON_SALE)
            ->where('tree.is_sold', false)
            ->where('tree.is_young', true)
            ->where('first_sale.is_pending', false)
            ->where('tree.is_pending', false)
            ->where(function (Builder $query) {
                $query->where('tree.sale_frozen_to','<',CDateTime::getCurrentDate())
                    ->orWhereNull('tree.sale_frozen_to');
            })
            ->whereIn('tree.user_id', $investorsIds);
    }
    public function getInvestorsIds(): array
    {
        return $this->users
            ->from($this->users->getTable(). ' as users')
            ->leftJoin($this->roleUsers->getTable() . ' as roleUsers',
                'users.id',
                '=',
                'roleUsers.user_id'
            )
            ->where('roleUsers.role_id',Roles::INVESTORS)
            ->distinct()
            ->pluck('users.id')
            ->toArray();
    }
    public function getCountAvailableTrees(array $investorsIds): int
    {
        return $this->getBaseQuery($investorsIds)->count();
    }
    public function getAvailableTrees(int $countTrees,array $investorsIds): array
    {
        return $this->getBaseQuery($investorsIds)
            ->limit($countTrees)
            ->select([
                'first_sale.tree_id as id',
                'first_sale.price',
                'tree.planting_date',
            ])
            ->get()
            ->toArray();
    }
}
