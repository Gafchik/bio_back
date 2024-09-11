<?php

namespace App\Http\Classes\LogicalModels\Gift;

use App\Http\Classes\LogicalModels\Gift\Exceptions\GiftNotFoundException;

class Gift
{
    public function __construct(
        private GiftModel $model,
    ){}

    public function createGift(array $data): void
    {
        $this->model->createGift($data);
    }
    public function getGiftInfo(): array
    {
        return $this->model->getGiftInfo();
    }
    public function cancelMyGift(int $giftId): void
    {
        $transaction = $this->model->getMyGiftTransactionById($giftId);
        if(is_null($transaction)){
            throw new GiftNotFoundException();
        }
        $this->model->cancelMyGift($giftId,$transaction);
    }
    public function getGiftCertificateData(int $id): array
    {
        return $this->model->getGiftCertificateData($id);
    }
    public function getGiftByCode(string $code): void
    {
        if(!$this->model->checkGiftExist($code)){
            throw new GiftNotFoundException();
        }
        $this->model->getGiftByCode($code);
    }
}
