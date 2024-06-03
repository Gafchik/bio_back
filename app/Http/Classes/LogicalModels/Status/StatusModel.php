<?php

namespace App\Http\Classes\LogicalModels\Status;

use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Support\Facades\Auth;

class StatusModel
{
    public function __construct(
        private Trees $trees
    ){}

    public function getStatus(): int
    {
        return $this->trees
            ->where('user_id', Auth::id())
            ->count();
    }
}
