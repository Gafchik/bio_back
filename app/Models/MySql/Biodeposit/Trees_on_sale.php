<?php

namespace App\Models\MySql\Biodeposit;

use App\Models\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
class Trees_on_sale extends BaseModel
{
    use NodeTrait;
    protected $connection = 'biodeposit';
    protected $table = 'trees_on_sale';
    protected $fillable = [
        'tree_id',
        'price',
        'commission',
        'is_pending',
        'created_at',
        'updated_at',
    ];

    protected $casts_integer = [
        'price',
    ];
}
