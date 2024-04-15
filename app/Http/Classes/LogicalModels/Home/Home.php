<?php

namespace App\Http\Classes\LogicalModels\Home;

class Home
{
    public function __construct(
        private HomeModel $model
    ){}
    public function getInfo(): array
    {
        return [
            'all_count_trees' => $this->model->getAllCountTrees(),
            'price_to_liter' => $this->model->getPriceToLiter(),
            'tree_all_price' => $this->model->getTreeAllPrice(),
            'count_transactions' => $this->model->getCountTransactions(),
            'count_yong_trees' => $this->model->getCountYongTrees(),
            'count_users' => $this->model->getCountUsers(),
            'videos' => $this->model->getVideos(),
            'first_news' => $this->model->getFirstNews(),
        ];
    }
}
