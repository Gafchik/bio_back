<?php

namespace App\Http\Classes\LogicalModels\UserMap;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;

class UserMap
{
    public function __construct(
        private UserMapModel $model
    ){}
    public function getTrees(): array
    {
        $trees = $this->model->getTrees();
        $fieldIds = TransformArrayHelper::getArrayUniqueByField($trees,'field_id');
        $fields = $this->model->getFields($fieldIds);
        return [
            'trees' => $trees,
            'fields' => $fields,
        ];
    }
}
