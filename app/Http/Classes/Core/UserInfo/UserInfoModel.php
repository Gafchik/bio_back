<?php

namespace App\Http\Classes\Core\UserInfo;

use App\Http\Classes\Structure\WalletsType;
use App\Models\MySql\Biodeposit\{
    Trees,
    Users,
    DemoBalance,
    DicCurrencies,
    UserInfo as UserInfoTable,
    Wallets
};
use Illuminate\Database\Eloquent\Builder;

class UserInfoModel
{
    public function __construct(
        private Users $userModel,
        private DemoBalance $demoBalance,
        private DicCurrencies $dicCurrencies,
        private UserInfoTable $userInfo,
        private Wallets $wallets,
        private Trees $trees,
    )
    {}
    public function getUuidBuyId(int $id): ?string
    {
        return $this->userModel
            ->where('id',$id)
            ->first()
            ?->value('uuid');
    }
    public function getUserInfo(?string $uuid = null, ?int $id = null): array
    {
        $query = $this->getBaseUserQuery();
        if(!!$uuid){
            $query->where('userModel.uuid',$uuid);
        }
        if(!!$id){
            $query->where('userModel.id',$id);
        }
        $query->select([
                'userModel.id',
                'userModel.uuid',
                'userModel.email',
                'userModel.permissions',
                'userModel.last_login',
                'userModel.referral_link',
                'userModel.created_at',
                'userModel.updated_at',
                'userModel.deleted_at',
                'userModel.deleted_email',
                'userModel.enable_2_fact',
                'userModel.is_active_user',
                'userInfo.first_name',
                'userInfo.last_name',
                'userInfo.phone',
                'userInfo.gender',
                'userInfo.birthday',
                'userInfo.avatar',
                'userInfo.created_at',
                'userInfo.updated_at',
                'userInfo.codePromo',
                'userInfo.level',
                'userModel.google2fa_secret',
            ]);
        $user = $query->first()
            ?->toArray();
        $user['demo_balance'] = $this->getDemoBalance($user['id']);
        $user['trees'] = $this->getUserTress($user['id']);

        $wallets = $this->getWallets($user['id']);
        foreach ($wallets as $wallet){
            if($wallet['type'] === WalletsType::LIVE_PAY){
                $user['wallet_live_pay_id'] = $wallet['id'];
                $user['wallet_live_pay_balance'] = $wallet['balance'];
            }else if($wallet['type'] === WalletsType::BONUS){
                $user['wallet_bonus_id'] = $wallet['id'];
                $user['wallet_bonus_balance'] = $wallet['balance'];
            }else if($wallet['type'] === WalletsType::FUTURES){
                $user['wallet_futures_id'] = $wallet['id'];
                $user['wallet_futures_balance'] = $wallet['balance'];
            }
        }
        return $user;
    }
    private function getBaseUserQuery(): Builder
    {
        return $this->userModel
            ->from($this->userModel->getTable(). ' as userModel')
            ->leftJoin($this->userInfo->getTable() . ' as userInfo',
                'userModel.id',
                '=',
                'userInfo.user_id'
            );
    }
    private function getDemoBalance(int $userId): array
    {
        return $this->demoBalance
            ->from($this->demoBalance->getTable(). ' as demoBalance')
            ->leftJoin($this->dicCurrencies->getTable() . ' as dicCurrencies',
                'demoBalance.currency_id',
                '=',
                'dicCurrencies.id'
            )
            ->select([
                'demoBalance.id',
                'demoBalance.balance',
                'dicCurrencies.name as currency_name',
            ])
            ->where('demoBalance.user_id',$userId)
            ->get()
            ->toArray();
    }
    private function getWallets(int $userId): array
    {
        return $this->wallets
            ->where('user_id',$userId)
            ->select([
                'id',
                'type',
                'balance',
            ])
            ->get()
            ->toArray();
    }
    private function getUserTress(int $userId): array
    {
        return $this->trees
            ->where('user_id',$userId)
            ->get()
            ->toArray();
    }
}
