<?php

namespace App\Http\Classes\LogicalModels\SignedDocuments;
use Illuminate\Support\Facades\Auth;
use App\Models\MySql\Biodeposit\{Certificates,
    Transactions,
    Tree_types,
    Users,
    Wallets,
    UserInfo,
    Payments,
    Orders,
    Order_details,
    Dic_transactions_status,
    Dic_transactions_type,
    Trees,
    Fields,
    Cooperatives,
    Provinces,
    ProvinceTranslations,
    Locations,
    LocationTranslations,
    Cooperative_translations,
    Tree_type_translations};
class SignedDocumentsModel
{
    public function __construct(
        private Transactions $transactions,
        private Users $users,
        private Wallets $wallets,
        private Tree_types $tree_types,
        private Certificates $certificates,
        private UserInfo $userInfo,
        private Payments $payments,
        private Orders $orders,
        private Order_details $orderDetails,
        private Dic_transactions_status $transactionsStatus,
        private Dic_transactions_type $transactionsType,
        private Trees $trees,
        private Fields $fields,
        private Cooperatives $cooperatives,
        private Provinces $provinces,
        private ProvinceTranslations $provinceTranslations,
        private Locations $locations,
        private LocationTranslations $locationTranslations,
        private Cooperative_translations $cooperativeTranslations,
        private Tree_type_translations $tree_type_translations,
    ){}
    public function getTreesInfo(array $data): array
    {
        $query = $this->trees
            ->from($this->trees->getTable() . ' as trees')
            ->leftJoin($this->fields->getTable() . ' as fields', 'trees.field_id', '=', 'fields.id')
            ->leftJoin($this->tree_type_translations->getTable() . ' as tree_type_translations', function ($join) {
                $join->on('tree_type_translations.tree_type_id', '=', 'trees.tree_type_id');
                $join->where('tree_type_translations.locale', '=', 'ru');
            })
            ->leftJoin($this->cooperatives->getTable() . ' as cooperatives', 'fields.cooperative_id', '=', 'cooperatives.id')
            ->leftJoin($this->cooperativeTranslations->getTable() . ' as cooperativeTranslations', function ($join) {
                $join->on('cooperatives.id', '=', 'cooperativeTranslations.cooperative_id');
                $join->where('cooperativeTranslations.locale', '=', 'ru');
            })
            ->leftJoin($this->provinces->getTable() . ' as provinces', 'cooperatives.province_id', '=', 'provinces.id')
            ->leftJoin($this->provinceTranslations->getTable() . ' as provinceTranslations', function ($join) {
                $join->on('provinces.id', '=', 'provinceTranslations.province_id');
                $join->where('provinceTranslations.locale', '=', 'ru');
            })
            ->leftJoin($this->locations->getTable() . ' as locations', 'provinces.location_id', '=', 'locations.id')
            ->leftJoin($this->locationTranslations->getTable() . ' as locationTranslations', function ($join) {
                $join->on('locations.id', '=', 'locationTranslations.location_id');
                $join->where('locationTranslations.locale', '=', 'ru');
            })
            ->leftJoin($this->users->getTable() . ' as user', 'user.id', '=', 'trees.user_id')
            ->leftJoin($this->tree_types->getTable() . ' as tree_types', 'tree_types.id', '=', 'trees.tree_type_id')
            ->leftJoin($this->userInfo->getTable() . ' as userInfo', 'user.id', '=', 'userInfo.user_id')
            ->leftJoin($this->certificates->getTable() . ' as certificates', 'trees.id', '=', 'certificates.tree_id')
            ->leftJoin($this->provinceTranslations->getTable() . ' as provinceTranslations_en', function ($join) {
                $join->on('provinces.id', '=', 'provinceTranslations_en.province_id');
                $join->where('provinceTranslations_en.locale', '=', 'en');
            })
            ->leftJoin($this->locationTranslations->getTable() . ' as locationTranslations_en', function ($join) {
                $join->on('locations.id', '=', 'locationTranslations_en.location_id');
                $join->where('locationTranslations_en.locale', '=', 'en');
            })
            ->select([
                'trees.id',                         // ID дерева
                'trees.uuid',                       // UUID дерева
                'trees.purchase_price',
                'trees.current_price',
                'trees.coordinates',                // Координаты
                'trees.planting_date',              // Дата посадки
                'trees.initial_price',              // Начальная цена
                'fields.cadastral_number',          // Кадастровый номер поля
                'cooperativeTranslations.name as cooperative_name',
                'tree_type_translations.title as tree_type',
                'tree_types.slug',                  // Slug типа дерева
                'userInfo.first_name',              // Имя владельца
                'userInfo.last_name',               // Фамилия владельца
                'fields.cadastral_number as field_number', // Кадастровый номер поля
                'trees.planting_date',              // Дата посадки
                'trees.purchase_date',              // Дата покупки
                'trees.current_price',              // Текущая цена
                'certificates.created_at',          // Дата создания сертификата
                'certificates.created_at as certificates_inst_data', // Дата создания сертификата
            ])
            ->selectRaw("concat('(', locationTranslations.name, '), ', '(', provinceTranslations.name, ')') as location");

        if (!empty($data['ids'])) {
            $query->whereIn('trees.id', $data['ids']);
        }

        if (!empty($data['uuid'])) {
            $query->where('trees.uuid', $data['uuid']);
        }

        return $query->get()->toArray();
    }
    public function getUserDataByDoc(): array
    {
        return $this->users
            ->from($this->users->getTable(). ' as users')
            ->leftJoin($this->userInfo->getTable() . ' as userInfo',
                'users.id',
                '=',
                'userInfo.user_id'
            )
            ->where('users.id',Auth::user()->id)
            ->first([
                'userInfo.first_name',
                'userInfo.last_name',
                'users.email',
                'userInfo.phone',
            ])
            ->toArray();
    }
    public function getOrderByTreeId(int $treeId): array
    {
        return $this->orders
            ->from($this->orders->getTable(). ' as orders')
            ->join($this->orderDetails->getTable() . ' as orderDetails',
                'orders.id',
                '=',
                'orderDetails.order_id'
            )
            ->where('orderDetails.tree_id', $treeId)
            ->first([
                'orders.user_id',
                'orders.transaction_id',
                'orders.status',
                'orders.trees_count',
                'orders.total',
                'orders.signed_documents',
                'orders.from_primary_market',
                'orders.created_at',
                'orders.updated_at',
                'orders.uuid'
            ])
            ->toArray();
    }
    public function signed(array $data): void
    {
        $this->trees
            ->where('uuid', $data['uuid'])
            ->update([
                'signed_documents' => true
            ]);
    }
}
