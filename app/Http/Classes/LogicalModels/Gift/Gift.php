<?php

namespace App\Http\Classes\LogicalModels\Gift;

class Gift
{
    public function __construct(
        private GiftModel $model,
    ){}

    public function createGift(array $data): void
    {
        $this->model->createGift($data);
    }
}
