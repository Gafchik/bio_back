<?php

namespace App\Http\Classes\LogicalModels\UserMap;

use App\Models\MySql\Biodeposit\Fields;
use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Support\Facades\Auth;

class UserMapModel
{
    public function __construct(
        private Trees $trees,
        private Fields $fields,
    ){}

    public function getTrees(): array
    {
        return $this->trees
            ->where('user_id',Auth::user()->id)
            ->select([
                'uuid',
                'purchase_price',
                'planting_date',
                'season',
                'field_id',
                'coordinates',
            ])
            ->get()
            ->toArray();
    }
    public function getFields(array $fieldIds): array
    {
        if(empty($fieldIds)){
            return [];
        }
        return $this->fields
            ->whereIn('id', $fieldIds)
            ->select([
                'id',
                'area',
            ])
            ->get()
            ->toArray();
    }
}
