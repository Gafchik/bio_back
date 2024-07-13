<?php

namespace App\Models\MySql\Biodeposit;

use App\Models\BaseModel;

class Variables extends BaseModel
{
    protected $connection = 'biodeposit';
    protected $table = 'variables';
    public static function getValueByKey($key)
    {
        $variabe = self::where('key', $key)->first();
        return $variabe->value;
    }
}
