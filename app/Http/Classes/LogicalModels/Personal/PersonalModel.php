<?php

namespace App\Http\Classes\LogicalModels\Personal;

use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Support\Facades\Auth;

class PersonalModel
{
    public function __construct(
        private Trees $trees
    ){}

    public function getTrees(): array
    {
        return $this->trees
            ->where('user_id', Auth::id())
            ->get()
            ->toArray();
    }
}
