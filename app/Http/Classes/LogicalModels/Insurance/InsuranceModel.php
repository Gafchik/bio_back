<?php

namespace App\Http\Classes\LogicalModels\Insurance;

use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\CurrencyName;
use App\Http\Classes\Structure\TransactionTypes;
use App\Http\Facades\UserInfoFacade;
use App\Models\MySql\Biodeposit\Dic_insurance_type;
use App\Models\MySql\Biodeposit\Insurance;
use App\Models\MySql\Biodeposit\Tmp_insurance_requests;
use App\Models\MySql\Biodeposit\Transactions;
use App\Models\MySql\Biodeposit\Trees;
use App\Models\MySql\Biodeposit\Users;
use App\Models\MySql\Biodeposit\Wallets;
use Illuminate\Support\Facades\Auth;

class InsuranceModel
{
    public function __construct(
        private Tmp_insurance_requests $insuranceRequest,
        private Dic_insurance_type $dic_insurance_type,
        private Trees $trees,
        private Insurance $insurance,
        private Transactions $transactions,
        private Wallets $wallets,
        private Users $users,
    ){}
    public function getInsuranceTypes(): array
    {
        return $this->dic_insurance_type
            ->get()
            ->toArray();
    }
    public function getInsuranceTrees(int $userId): array
    {
        return $this->insurance
            ->from($this->insurance->getTable(), 'i')
            ->join($this->trees->getTable() . ' as t',
                't.id',
                '=',
                'i.tree_id'
            )
            ->join($this->users->getTable() . ' as u',
                'u.id',
                '=',
                'i.user_id'
            )
            ->join($this->dic_insurance_type->getTable() . ' as dt',
                'dt.id',
                '=',
                'i.type_id'
            )
            ->where('i.user_id',$userId)
            ->get([
                'i.user_id',
                'i.tree_id',
                'i.type_id',
                'i.transaction_id',
                'i.inst_date',
                't.uuid',
                'u.email',
                'dt.name',
                'dt.percent',
            ])
            ->toArray();
    }
    public function getInsuranceTreesById($id): ?array
    {
        return $this->insurance
            ->from($this->insurance->getTable(), 'i')
            ->join($this->trees->getTable() . ' as t',
                't.id',
                '=',
                'i.tree_id'
            )
            ->join($this->users->getTable() . ' as u',
                'u.id',
                '=',
                'i.user_id'
            )
            ->join($this->dic_insurance_type->getTable() . ' as dt',
                'dt.id',
                '=',
                'i.type_id'
            )
            ->where('i.tree_id',$id)
            ->select([
                'i.user_id',
                'i.tree_id',
                'i.type_id',
                'i.transaction_id',
                'i.inst_date',
                't.uuid',
                'u.email',
                'dt.name',
                'dt.percent',
            ])
            ->selectRaw('TIMESTAMPDIFF(YEAR, t.planting_date, CURDATE()) AS age')
            ->first()
            ?->toArray();
    }
    public function getTrees(array $ids): array
    {
        return $this->trees
            ->whereIn('id',$ids)
            ->get()
            ->toArray();
    }
    public function checkBalance(int $price): bool
    {
        $user = UserInfoFacade::getUserInfo(id:Auth::user()?->id);
        $wallet = $this->wallets
            ->where('id', $user['wallet_live_pay_id'])
            ->first()
            ?->toArray();
        return ($wallet['balance'] ?? 0) >= $price;
    }
    public function createInsurance(array $data, int $price,array $currentInsuranceType): void
    {
        $user = UserInfoFacade::getUserInfo(id:Auth::user()?->id);
        $this->insurance->getConnection()
            ->transaction(function () use ($data,$price,$user,$currentInsuranceType){
                $transactionId = $this->createTransaction($data,$price,$user);
                $this->deductFromWalletBalance($price,$user['wallet_live_pay_id']);
                if(!!$currentInsuranceType['has_cashback']){
                    $this->setCashback($currentInsuranceType,$price,$user['wallet_bonus_id']);
                }
                $insuranceData = [];
                foreach($data['ids'] as $treeId){
                    $insuranceData[] = [
                        'user_id' => $user['id'],
                        'tree_id' => $treeId,
                        'type_id' => $data['type'],
                        'transaction_id' => $transactionId
                    ];
                }
                $this->insurance->insert($insuranceData);
            });
    }
    private function createTransaction(array $data, int $price, array $user): int
    {
        return $this->transactions
            ->insertGetId([
                'wallet_id' => $user['wallet_live_pay_id'], //айди кошелька баланса
                'type' => TransactionTypes::INSURANCE,
                'amount' => $price,
                'commission' => 0,
                'total' => $price,
                'tree_count' => count($data['ids']),
                'data' => json_encode($data['ids']),
                'status' => 1,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => CurrencyName::USD['name'],
                'payment_service' => 1,
            ]);
    }
    private function deductFromWalletBalance(int $price, int $walletLivePayId): void
    {
        $this->wallets
            ->where('id', $walletLivePayId)
            ->decrement('balance', $price);
    }
    private function setCashback(array $currentInsuranceType,int $price,int $walletBonusId): void
    {
        $cashback = $price * (int)$currentInsuranceType['cashback_percent'] / 100;
        $this->wallets
            ->where('id', $walletBonusId)
            ->increment('balance', $cashback);
        $this->transactions
            ->insert([
                'wallet_id' => $walletBonusId, //айди кошелька бонуса
                'type' => TransactionTypes::TOP_UP,
                'amount' => $cashback,
                'commission' => 0,
                'total' => $cashback,
                'tree_count' => 0,
                'data' => null,
                'status' => 1,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
                'exchange_currency' => CurrencyName::USD['name'],
                'payment_service' => 1,
                'message' => 'cashback'
            ]);
    }
}
