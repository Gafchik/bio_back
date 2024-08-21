<?php

namespace App\Models\MySql\Biodeposit;

use App\Models\BaseModel;

class Tmp_insurance_requests extends BaseModel
{
    protected $connection = 'biodeposit';
    protected $table = '_tmp_insurance_requests';
}
