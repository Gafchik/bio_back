<?php

namespace App\Http\Classes\LogicalModels\Auth;

class Auth
{
    public function __construct(
        private AuthModel $model
    ){}
    public function checkEmail(array $data): bool
    {
        return $this->model->checkEmail($data);
    }
    public function reg(array $data): bool
    {
        return $this->model->reg($data);
    }
}
