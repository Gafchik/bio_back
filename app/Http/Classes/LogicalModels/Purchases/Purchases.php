<?php

namespace App\Http\Classes\LogicalModels\Purchases;

class Purchases
{
    public function __construct(
        private PurchasesModel $model
    ){}

    public function getPurchases(): array
    {
        return $this->model->getPurchases();
    }
    public function getTreeByOrderId(array $data): array
    {
        $orderTreeIds = $this->model->getOrderTreeIds($data['order_id']);
        return $this->model->getTreeByOrderIds($orderTreeIds);
    }
    public function getDocumentData(int $id): array
    {
        return $this->model->getDocumentData($id);
    }
}
