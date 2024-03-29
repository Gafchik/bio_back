<?php

namespace App\Http\Classes\LogicalModels\News;

use App\Http\Classes\LogicalModels\News\Exceptions\NewsNotFoundException;

class News
{
    public function __construct(
        private NewsModel $model
    ){}

    public function getCards(int $page): array
    {
        return $this->model->getCards($page);
    }
    public function getCardsDetails(int $id): ?array
    {
        $result = $this->model->getCardsDetails($id);
        if(!$result){
            throw  new NewsNotFoundException();
        }
        return $result;
    }
    public function addView(int $id): void
    {
        $this->model->addView($id);
    }
}
