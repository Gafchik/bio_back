<?php

namespace App\Models\MySql\Biodeposit;

use App\Models\BaseModel;

class Videos extends BaseModel
{
    protected $connection = 'biodeposit';
    protected $table = 'videos';
}
