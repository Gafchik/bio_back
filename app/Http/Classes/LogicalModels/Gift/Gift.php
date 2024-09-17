<?php

namespace App\Http\Classes\LogicalModels\Gift;

use App\Http\Classes\LogicalModels\Gift\Exceptions\GiftNotFoundException;
use App\Http\Classes\Structure\CDateTime;

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
        $result = $this->model->getGiftInfo();
        $result = array_values(array_filter($result, fn($item) => !!$item['i_gave']
            || is_null($item['notification_date'])
            || CDateTime::convertDateToDateFormat($item['notification_date'],CDateTime::DATETIME_FORMAT_DB) <= CDateTime::getCurrentDate()
        ));
        return$result;
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
