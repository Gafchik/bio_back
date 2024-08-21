<?php

namespace App\Http\Classes\LogicalModels\Insurance;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Classes\LogicalModels\Insurance\Exceptions\NoMoneyException;
use App\Http\Classes\LogicalModels\Insurance\Exceptions\TreeNotFoundException;
use App\Http\Facades\UserInfoFacade;
use Illuminate\Support\Facades\Auth;

class Insurance
{
    public function __construct(
        private InsuranceModel $model
    ){}

    public function getInsuranceTrees(): array
    {
        return $this->model->getInsuranceTrees(Auth::user()?->id);
    }
    public function getInsuranceTypes(): array
    {
        return $this->model->getInsuranceTypes();
    }
    public function getTemplateData($id): array
    {
        $tree = $this->model->getInsuranceTreesById($id);
        $user = UserInfoFacade::getUserInfo(id:Auth::user()?->id);
        if(empty($tree)){
            throw new TreeNotFoundException();
        }
        return [
            'tree' => $tree,
            'user' => [
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'phone' => $user['phone'],
            ],
        ];
    }
    public function createInsurance(array $data): void
    {
        $currentInsuranceType = TransformArrayHelper::callbackSearchFirstInArray(
            array: $this->model->getInsuranceTypes(),
            callback: fn($el) => $el['id'] === (int)$data['type'],
        );
        $insuranceTreeIds = array_column($this->getInsuranceTrees(), 'tree_id');
        $trees = TransformArrayHelper::callbackSearchAllInArray(
            array: $this->model->getTrees($data['ids']),
            callback: fn($el) => !in_array($el['id'], $insuranceTreeIds),
        );
        if(!!count($trees)){
            $price = $this->calcPrice($trees, $currentInsuranceType['percent']);
            if(!$this->model->checkBalance($price)){
                throw new NoMoneyException();
            }
            $this->model->createInsurance($data,$price,$currentInsuranceType);
        }
    }
    private function calcPrice(array $trees, float $percent): int
    {
        $price = 0;
        foreach($trees as $tree){
            $price += ceil($tree['current_price'] * $percent /100);
        }
        return $price;
    }
}
