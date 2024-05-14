<?php

namespace App\Http\Classes\LogicalModels\Personal;

class Personal
{
    public function __construct(
        private PersonalModel $model,
    ){}
    public function getTrees(): array
    {
        return $this->model->getTrees();
    }
}
