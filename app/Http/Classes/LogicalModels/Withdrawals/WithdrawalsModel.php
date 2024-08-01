<?php

namespace App\Http\Classes\LogicalModels\Withdrawals;

use App\Http\Classes\LogicalModels\Withdrawals\Exceptions\WithdrawalsSaveException;
use App\Http\Classes\MailModels\ActivationMail\ForgotPasswordMailModel;
use App\Http\Classes\MailModels\Withdrawals\WithdrawalsMailModel;
use App\Http\Classes\Structure\CDateTime;
use App\Models\MySql\Biodeposit\Wallets;
use App\Models\MySql\Biodeposit\Withdraws;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class WithdrawalsModel
{
    public function __construct(
        private Wallets $wallets,
        private Withdraws $withdraws,
    ){}

    public function getWallet(): array
    {
        return $this->wallets
            ->where('user_id',Auth::user()->id)
            ->whereNull('type')
            ->first()
            ->toArray();
    }
    public function fillReport(array $data, array $wallet, int $centAmount): void
    {
        $this->withdraws->getConnection()->beginTransaction();
        try {
            $this->wallets
                ->where('id',$wallet['id'])
                ->update([
                    'balance' => $wallet['balance'] - $centAmount,
                    'reserved' => ($wallet['reserved'] ?? 0) + $centAmount,
                ]);
            $this->withdraws
                ->insert([
                    'user_id' => Auth::user()->id,
                    'account_number' => $data['account_number'],
                    'bank' => $data['bank'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'type' => $data['type'],
                    'full_name' => $data['full_name'],
                    'date' => CDateTime::getCurrentDate(CDateTime::DATE_FORMAT_DB),
                    'amount' => $centAmount,
                    'status' => 0,
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                ]);
            foreach (config('emails.withdrawals') as $email) {
                Mail::to($email)
                    ->send(new WithdrawalsMailModel([
                        'full_name' => $data['full_name'],
                        'amount' => $centAmount /100,
                    ]));

            }
        $this->withdraws->getConnection()->commit();
        }catch (\Throwable $e){
            $this->withdraws->getConnection()->rollBack();
            throw new WithdrawalsSaveException();
        }
    }
}
