<?php

namespace App\Http\Classes\LogicalModels\Store;

class Store
{
    public function __construct(
        private StoreModel $model
    ){}
    public function getTreeStore(): array
    {
        return $this->model->getTreeStore();
    }
    public function getTreeByYear(array $data): array
    {
        return $this->model->getTreeByYear($data);
    }
}
