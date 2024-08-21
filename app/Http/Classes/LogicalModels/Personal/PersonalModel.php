<?php

namespace App\Http\Classes\LogicalModels\Personal;

use App\Models\MySql\Biodeposit\Insurance;
use App\Models\MySql\Biodeposit\Trees;
use Illuminate\Support\Facades\Auth;

class PersonalModel
{
    public function __construct(
        private Trees $trees,
        private Insurance $insurance,
    ){}

    public function getTrees(): array
    {
        return $this->trees
            ->from($this->trees->getTable(), 't')
            ->leftJoin($this->insurance->getTable() . ' as i',
                't.id',
                '=',
                'i.tree_id'
            )
            ->selectRaw('*')
            ->addSelect([
                't.id as id',
                'i.id as insurance_id',
            ])
            ->where('t.user_id', Auth::id())
            ->get()
            ->toArray();
    }
}
