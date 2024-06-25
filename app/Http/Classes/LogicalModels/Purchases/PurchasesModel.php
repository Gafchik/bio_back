<?php

namespace App\Http\Classes\LogicalModels\Purchases;

use Illuminate\Support\Facades\Auth;
use App\Models\MySql\Biodeposit\{Cooperative_translations,
    Cooperatives,
    Dic_transactions_status,
    Dic_transactions_type,
    Fields,
    Locations,
    LocationTranslations,
    Orders,
    Order_details,
    Payments,
    Provinces,
    ProvinceTranslations,
    Transactions,
    Tree_type_translations,
    Trees,
    User_setting,
    UserInfo,
    Users,
    Wallets};

class PurchasesModel
{
    public function __construct(
        private Orders $orders,
        private Order_details $orderDetails,
        private Trees $trees,
        private Users $users,
        private UserInfo $userInfo,
        private User_setting $userSetting,
        private Fields $fields,
        private Cooperatives $cooperatives,
        private Provinces $provinces,
        private ProvinceTranslations $provinceTranslations,
        private Locations $locations,
        private LocationTranslations $locationTranslations,
        private Cooperative_translations $cooperativeTranslations,
        private Tree_type_translations $tree_type_translations,
    ){}

    public function getPurchases(): array
    {
        return $this->orders
            ->where('user_id',Auth::user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }
    public function getTreeByOrderIds(array $ids): array
    {
        return $this->trees
            ->whereIn('id',$ids)
            ->get()
            ->toArray();
    }
    public function getOrderTreeIds(int $orderId): array
    {
        return $this->orderDetails
            ->where('order_id',$orderId)
            ->pluck('tree_id')
            ->toArray();
    }
    public function getDocumentData(int $id): array
    {
        $order = $this->orders
            ->where('id',$id)
            ->first()
            ?->toArray();
        $user =  $this->getUserInfo(id:$order['user_id']);
        $treeIds = $this->orderDetails
            ->where('order_id',$order['id'])
            ->pluck('tree_id')
            ->toArray();
        return [
            'order' => $order,
            'trees' => $this->getTreesInfoByDoc($treeIds),
            'user' => $user,
        ];
    }
    public function getUserInfo(int $id): ?array
    {
        $result = $this->users
            ->from($this->users->getTable(). ' as userModel')
            ->leftJoin($this->userInfo->getTable() . ' as userInfo',
                'userModel.id',
                '=',
                'userInfo.user_id'
            )
            ->leftJoin($this->userSetting->getTable() . ' as userSetting',
                'userModel.id',
                '=',
                'userSetting.user_id'
            )
            ->where('userModel.id',$id)
            ->select([
                'userModel.id',
                'userModel.email',
                'userSetting.locale',
                'userModel.permissions',
                'userModel.is_active_user',
                'userInfo.first_name as lastName',
                'userInfo.last_name as firstName',
                'userInfo.phone',
                'userSetting.locale',
                'userSetting.promocode',
                'userInfo.level',
                'userModel.google2fa_secret as secret_key',
            ])
            ->selectRaw('!ISNULL(userModel.google2fa_secret) as has_2fa_code')
            ->first()
            ?->toArray();
        return $result;
    }
    private function getTreesInfoByDoc(array $ids): array
    {
        return $this->trees
            ->from($this->trees->getTable(). ' as trees')
            ->leftJoin($this->fields->getTable() . ' as fields',
                'trees.field_id',
                '=',
                'fields.id'
            )
            ->leftJoin($this->tree_type_translations->getTable() . ' as tree_type_translations',
                function ($join) {
                    $join->on('tree_type_translations.tree_type_id', '=', 'trees.tree_type_id');
                    $join->where('tree_type_translations.locale','=','ru');
                }
            )
            ->leftJoin($this->cooperatives->getTable() . ' as cooperatives',
                'fields.cooperative_id',
                '=',
                'cooperatives.id'
            )
            ->leftJoin($this->cooperativeTranslations->getTable() . ' as cooperativeTranslations',
                function ($join) {
                    $join->on('cooperatives.id', '=', 'cooperativeTranslations.cooperative_id');
                    $join->where('cooperativeTranslations.locale','=','ru');
                }
            )
            ->leftJoin($this->provinces->getTable() . ' as provinces',
                'cooperatives.province_id',
                '=',
                'provinces.id'
            )
            ->leftJoin($this->provinceTranslations->getTable() . ' as provinceTranslations',
                function ($join) {
                    $join->on('provinces.id', '=', 'provinceTranslations.province_id');
                    $join->where('provinceTranslations.locale','=','ru');
                }
            )
            ->leftJoin($this->locations->getTable() . ' as locations',
                'provinces.location_id',
                '=',
                'locations.id'
            )
            ->leftJoin($this->locationTranslations->getTable() . ' as locationTranslations',
                function ($join) {
                    $join->on('locations.id', '=', 'locationTranslations.location_id');
                    $join->where('locationTranslations.locale','=','ru');
                }
            )
            ->select([
                'trees.uuid',                                       //айди дерева
                'trees.purchase_price',
                'trees.current_price',
                'trees.uuid',                                       //айди дерева
                'trees.coordinates',                                //кордитаны
                'trees.planting_date',                              //дата посадки
                'trees.initial_price',                              //цена
                'fields.cadastral_number',          //номер поля
                'cooperativeTranslations.name as cooperative_name',
                'tree_type_translations.title as tree_type',
            ])
            ->selectRaw("concat('(', locationTranslations.name, '), ', '(', provinceTranslations.name, ')') as location")
            ->whereIn('trees.id',$ids)
            ->get()
            ->toArray();
    }
}
