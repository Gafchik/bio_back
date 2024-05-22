<?php

namespace App\Http\Classes\LogicalModels\SignedDocuments;

use App\Http\Classes\Structure\CDateTime;
use Illuminate\Support\Facades\Auth;

class SignedDocuments
{
    public function __construct(
        private SignedDocumentsModel $model,
    ){}
    public function getOfferData(array $data): array
    {
        return $this->model->getTreesInfo($data);
    }
    public function getUserDataByDoc(): array
    {
        return $this->model->getUserDataByDoc();
    }
    public function getOrderData(array $data): array
    {
        $treesInfo = $this->model->getTreesInfo($data);
        return $this->model->getOrderByTreeId($treesInfo[0]['id']);
    }
    public function getCertificateDara(array $data): array
    {
        $treesInfo = $this->model->getTreesInfo($data);
        return $this->prepareTemplateData($treesInfo[0]);
    }
    private function prepareTemplateData(array $data): array
    {
        $coordinates = json_decode($data['coordinates'], true);
        $data['lat'] = $coordinates['lat'];
        $data['lng'] = $coordinates['lng'];
        $data['current_price'] = number_format($data['current_price'] / 100, 2);
        $data['status'] = 'Purchased';
        $data['owner'] = 'Biodeposit';
        $year = CDateTime::getYear($data['planting_date']);
        $season = CDateTime::getSeason($data['planting_date']);
        $data['planting_date'] = $year . ' ' . $season;
        $data['certificates_inst_data'] = CDateTime::convertDateToDateFormat(
            $data['certificates_inst_data'],
            CDateTime::DATE_FORMAT_PEOP
        );
        $data['purchase_date'] = CDateTime::convertDateToDateFormat(
            $data['purchase_date'],
            CDateTime::DATE_FORMAT_PEOP
        );
        $data['created_at'] = CDateTime::convertDateToDateFormat(
            $data['created_at'],
            CDateTime::DATE_FORMAT_PEOP
        );

        return $data;
    }
    public function signed(array $data): void
    {
        $this->model->signed($data);
    }
}
