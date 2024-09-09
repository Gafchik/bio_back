<?php

namespace App\Http\Classes\LogicalModels\Gift;

use App\Http\Classes\MailModels\NotificationGift\NotificationGiftMailModel;
use Carbon\Carbon;
use App\Http\Classes\Structure\{
    GiftTypes,
    CDateTime,
};
use App\Http\Facades\UserInfoFacade;
use App\Models\MySql\Biodeposit\{
    Gifts,
    Gifted_trees,
    Transactions,
    Trees,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webpatser\Uuid\Uuid;

class GiftModel
{
    public function __construct(
        public Gifts        $gifts,
        public Gifted_trees $giftedTrees,
        public Transactions $transactions,
        public Trees $trees,
    ){}

    public function createGift(array $data): string
    {
        $user = UserInfoFacade::getUserInfo(id: Auth::user()?->id);
        $treeIds = array_column($data['treesToGift'], 'id');
        $giftUuid = Uuid::generate()->string;
        $this->gifts->getConnection()
            ->transaction(function () use ($data, $user, $treeIds, $giftUuid) {
                $transactionId = $this->transactions
                    ->insertGetId([
                        'wallet_id' => $user['wallet_live_pay_id'],
                        'type' => $data['type'],
                        'amount' => 0,
                        'commission' => 0,
                        'total' => 0,
                        'tree_count' => count($treeIds),
                        'data' => '[' . implode(',', $treeIds) . ']',
                        'crm_data' => null,
                        'error' => null,
                        'system_error' => null,
                        'message' => null,
                        'status' => 0,
                        'crm_sync' => null,
                        'payment_service' => null,
                        'exchange_rate' => null,
                        'created_at' => CDateTime::getCurrentDate(),
                        'updated_at' => CDateTime::getCurrentDate(),
                        'deleted_at' => null,
                        'promocode' => null,
                        'exchange_currency' => null,
                        'payment_uid' => null
                    ]);
                $giftId = $this->gifts->insertGetId([
                    'from_user_id' => $user['id'],
                    'email' => !!$data['isKnowUser'] ? $data['email'] : null,
                    'phone' => "",
                    'code' => $giftUuid,
                    'type' => 0,
                    'status' => 0,
                    'multiple' => 0,
                    'express' => 0,
                    'signed_documents' => 0,
                    'transaction_id' => $transactionId,
                    'created_at' => CDateTime::getCurrentDate(),
                    'updated_at' => CDateTime::getCurrentDate(),
                    'expired_at' => null,
                    'notification_date' => $data['notifyDate'] ?? null,
                    'is_notification' => false,
                ]);
                $treeToGiftData = [];
                foreach ($treeIds as $treeId) {
                    $treeToGiftData[] = [
                            'gift_id' => $giftId,
                            'tree_id' => $treeId,
                            'money' => 0,
                            'dividend_frozen_to' => $data['freezeMoneyYear'],
                            'sale_frozen_to' =>$data['type'] === GiftTypes::GIFT_DONATION->value
                                ? $data['freezeSellYear']
                                : 3,
                            'tree_gift_status_id' => $data['type'] === GiftTypes::GIFT_DONATION->value
                                ? 1
                                : 2,
                            'created_at' => CDateTime::getCurrentDate(),
                            'updated_at' => CDateTime::getCurrentDate(),
                        ];
                }
                $this->giftedTrees->insert($treeToGiftData);
                $this->trees->whereIn('id', $treeIds)->update([
                    'is_gifted' => 1,
                ]);
                if($data['isKnowUser']){
                    if(!is_null($data['notifyDate'])){
                        $currentDate = Carbon::today();
                        $notifyDate = Carbon::createFromFormat('Y/m/d', $data['notifyDate']);
                        if($notifyDate->isSameDay($currentDate)){
                            $this->sendMail($data['email'],$giftUuid);
                        }
                    }else{
                        $this->sendMail($data['email'],$giftUuid);
                    }
                    $this->gifts->where('id', $giftId)->update(['is_notification' => true]);
                }
            });
        return $giftUuid;
    }
    private function sendMail(string $email, string $code): void
    {
        Mail::to($email)
            ->send(new NotificationGiftMailModel([
                'email' => $email,
                'activationCode' => $code,
                'locale' => app()->getLocale(),
            ]));
    }
}
