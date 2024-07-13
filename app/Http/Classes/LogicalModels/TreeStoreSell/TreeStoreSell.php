<?php

namespace App\Http\Classes\LogicalModels\TreeStoreSell;

use App\Http\Classes\Helpers\TransformArray\TransformArrayHelper;
use App\Http\Classes\LogicalModels\TreeStoreSell\Exceptions\TreeOwnerException;
use App\Http\Classes\Structure\Roles;
use Illuminate\Support\Facades\Auth;

class TreeStoreSell
{
    public function __construct(
        public TreeStoreSellModel $model,
    ){}

    public function getPosition(array $data): array
    {
        $positions = [];
        foreach ($data['trees'] as $tree)
        {
            $newPosition = $this->model->getPosition($tree['commission']);
            $tempPosition = $newPosition;
            foreach ($positions as $key => $value) {
                if ($value['temp_position'] > $tempPosition) {
                    $positions[$key]['position']++;
                } elseif ($value['temp_position'] == $tempPosition && $value['commission'] < $tree['commission']) {
                    $positions[$key]['position']++;
                } else {
                    $newPosition++;
                }
            }

            $positions[] = [
                'tree_id' => $tree['id'],
                'position' => $newPosition,
                'commission' => $tree['commission'],
                'temp_position' => $tempPosition
            ];
        }
        return $positions;
    }
    public function sell(array $data): void
    {
        $user = Auth::user();
        $roles = $this->model->getRoles();
        $treeIds = array_column($data['trees'], 'id');
        $currentTrees = $this->model->getCurrenTrees($treeIds);
        $trees = [];
        foreach ($data['trees'] as $inputTree)
        {
            foreach ($currentTrees as $currentTree) {
                if($currentTree['id'] === $inputTree['id']) {
                    $trees[] = [
                        ...$inputTree,
                        ...$currentTree,
                    ];
                }
            }
        }
        $this->checkTreesOwner($user,$trees);
        $this->model->sell($trees,$roles,$treeIds);

    }
    private function checkTreesOwner($user,array $currentTrees): void
    {
        $ownerId = array_unique(array_column($currentTrees, 'user_id'));
        if(count($ownerId)>1){
            throw new TreeOwnerException();
        }
        if($ownerId[0] !== $user->id){
            throw new TreeOwnerException();
        }
    }
    public function getTreeInSell(): array
    {
        return $this->model->getTreeInSell();
    }
    public function removeSell(array $data): void
    {
        $user = Auth::user();
        $treeInSell = $this->model->getTreeInSell();
        $treeIds = array_column($data['trees'], 'id');
        $treeInSell = TransformArrayHelper::callbackSearchAllInArray(
            array: $treeInSell,
            callback: fn($t) =>  in_array($t['id'],$treeIds)
        );
        $treeIds = array_column($treeInSell, 'id');
        $this->model->removeSell($treeInSell,$treeIds);
    }
}
