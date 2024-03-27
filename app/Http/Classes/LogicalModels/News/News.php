<?php

namespace App\Http\Classes\LogicalModels\News;

class News
{
    public function __construct(
        private NewsModel $model
    ){}

    public function getCards(int $page): array
    {
        return $this->model->getCards($page);
    }
}
