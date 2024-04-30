<?php

namespace App\Http\Classes\LogicalModels\User;

class User
{
    public function __construct(
        private UserModel $model
    ){}

    public function changeLocale(array $data): void
    {
        $this->model->changeLocale($data);
    }
}
