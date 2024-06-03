<?php

namespace App\Http\Classes\LogicalModels\Status;

class Status
{
    public function __construct(
        private StatusModel $model
    ){}
    public function getStatus(): array
    {
        return ['status' => $this->model->getStatus()];
    }
}
