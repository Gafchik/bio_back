<?php

namespace App\Http\Classes\LogicalModels\Common\WorkWithPromo;

use App\Models\MySql\Biodeposit\Wallets;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\User_setting;
use Illuminate\Support\Facades\Auth;

class WorkWithPromoModel
{
    public function __construct(
        private User_setting $userSetting,
        private Transactions $transactions,
        private Wallets $wallets,
    ){}
    public function getPromoCode(string $promoCode): ?array
    {
        return $this->userSetting
            ->where('promocode',$promoCode)
            ->select([
                'user_id',
                'promocode',
                'promocode_discount',
                'promocode_bonus',
                'promocode_wallet',
                'promocode_multiple',
                'promocode_area',
                'promocode_tree_min',
                'promocode_tree_max',
            ])
            ->first()
            ?->toArray();
    }
    public function checkUsingInTransaction(array $promoCode): bool
    {
        return $this->transactions
            ->where('promocode',$promoCode['promocode'])
            ->whereIn('wallet_id',$this->getWallets())
            ->exists();
    }
    private function getWallets(): array
    {
        return $this->wallets
            ->where('user_id', Auth::user()?->id)
            ->pluck(
                'id',
            )
            ->toArray();
        }
}
