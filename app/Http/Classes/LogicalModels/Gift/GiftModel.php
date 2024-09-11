<?php

namespace App\Http\Classes\LogicalModels\Gift;

use App\Http\Classes\MailModels\NotificationGift\NotificationGiftMailModel;
use Carbon\Carbon;
use App\Http\Classes\Structure\{GiftTypes, CDateTime, TransactionStatus};
use App\Http\Facades\UserInfoFacade;
use App\Models\MySql\Biodeposit\{
    Gifts,
    Gifted_trees,
    Transactions,
    Trees,
    Users,
    UserInfo,
    Fields,
    Orders,
    Order_details,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webpatser\Uuid\Uuid;

class GiftModel
{
    public function __construct(
        public Gifts         $gifts,
        public Gifted_trees  $giftedTrees,
        public Transactions  $transactions,
        public Trees         $trees,
        public Users         $users,
        public UserInfo      $userInfo,
        public Fields        $fields,
        public Orders        $orders,
        public Order_details $orderDetails,
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
                $giftId = $this->gifts
                    ->insertGetId([
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
                        'sale_frozen_to' => $data['type'] === GiftTypes::GIFT_DONATION->value
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
                if ($data['isKnowUser']) {
                    if (!is_null($data['notifyDate'])) {
                        $currentDate = Carbon::today();
                        $notifyDate = Carbon::createFromFormat('Y/m/d', $data['notifyDate']);
                        if ($notifyDate->isSameDay($currentDate)) {
                            $this->sendMail($data['email'], $giftUuid,$giftId);
                        }
                    } else {
                        $this->sendMail($data['email'], $giftUuid,$giftId);
                    }
                }
            });
        return $giftUuid;
    }

    private function sendMail(string $email, string $code, int $giftId): void
    {
        Mail::to($email)
            ->send(new NotificationGiftMailModel([
                'email' => $email,
                'activationCode' => $code,
                'locale' => app()->getLocale(),
            ]));
        $this->gifts->where('id', $giftId)
            ->update([
                'is_notification' => true,
                'updated_at' => CDateTime::getCurrentDate(),
            ]);
    }

    public function getGiftInfo(): array
    {
        $user = UserInfoFacade::getUserInfo(id: Auth::user()?->id);
        return $this->gifts
            ->from($this->gifts->getTable(), 'g')
            ->leftJoin($this->transactions->getTable() . ' as t',
                't.id',
                '=',
                'g.transaction_id',
            )
            ->leftJoin($this->orders->getTable() . ' as o',
                't.id',
                '=',
                'o.transaction_id',
            )
            ->leftJoin($this->userInfo->getTable() . ' as u',
                'u.user_id',
                '=',
                'g.from_user_id',
            )
            ->select([
                'o.id as order_id',
                'g.id',
                'g.from_user_id',
                'g.email as user_to',
                'g.code',
                'g.status',
                't.tree_count',
            ])
            ->selectRaw("if(g.from_user_id = ?,1,0) as i_gave", [$user['id']])
            ->selectRaw("CONCAT(first_name, ' ',last_name) as user_from")
            ->where('g.from_user_id', $user['id'])
            ->orWhere('g.email', $user['email'])
            ->orderByDesc('g.id')
            ->get()
            ->toArray();
    }

    public function getMyGiftTransactionById(int $id): ?array
    {
        $user = UserInfoFacade::getUserInfo(id: Auth::user()?->id);
        return $this->gifts
            ->from($this->gifts->getTable(), 'g')
            ->leftJoin($this->transactions->getTable() . ' as t',
                't.id',
                '=',
                'g.transaction_id',
            )
            ->where('g.from_user_id', $user['id'])
            ->where('g.id', $id)
            ->select([
                't.data as tree_ids',
                't.id'
            ])
            ->first()
            ?->toArray();
    }

    public function cancelMyGift(int $giftId, array $transaction): void
    {
        $this->gifts->getConnection()
            ->transaction(function () use ($giftId, $transaction) {
                $this->gifts->where('id', $giftId)->delete();
                $this->transactions
                    ->where('id', $transaction['id'])
                    ->update([
                        'status' => TransactionStatus::WITHDRAWN,
                    ]);
                $treeIds = json_decode($transaction['tree_ids'], true);
                $this->trees
                    ->whereIn('id', $treeIds)
                    ->update(['is_gifted' => 0]);
            });
    }

    public function getGiftCertificateData(int $id): array
    {
        $gift = $this->gifts
            ->from($this->gifts->getTable(), 'g')
            ->leftJoin($this->transactions->getTable() . ' as t',
                't.id',
                '=',
                'g.transaction_id',
            )
            ->leftJoin($this->userInfo->getTable() . ' as u',
                'u.user_id',
                '=',
                'g.from_user_id',
            )
            ->select([
                'g.email as user_to',
                't.tree_count',
                't.data',
            ])
            ->selectRaw("CONCAT(first_name, ' ',last_name) as user_from")
            ->selectRaw("DATE_FORMAT(g.created_at, '%d.%m.%Y') AS created_at")
            ->where('g.id', $id)
            ->first()
            ->toArray();
        $treeIsd = json_decode($gift['data'], true);
        $trees = $this->trees
            ->from($this->trees->getTable(), 't')
            ->leftJoin($this->fields->getTable() . ' as f',
                'f.id',
                '=',
                't.field_id',
            )
            ->select([
                't.uuid',
                't.planting_date',
                't.current_price',
                'f.cadastral_number',
            ])
            ->whereIn('t.id', $treeIsd)
            ->get()
            ->toArray();
        return [
            'gift' => $gift,
            'trees' => $trees,
        ];
    }

    public function checkGiftExist(string $code): bool
    {
        $user = UserInfoFacade::getUserInfo(id: Auth::user()?->id);
        $gift = $this->gifts
            ->where('code', $code)
            ->first()
            ?->toArray();
        if (is_null($gift)) {
            return false;
        }
        if (!$gift['email']) {
            return true;
        }
        return $gift['email'] === $user['email'];
    }

    public function getGiftByCode(string $code): void
    {
        $user = UserInfoFacade::getUserInfo(id: Auth::user()?->id);
        $gift = $this->gifts->where('code', $code)->first()?->toArray();
        $transaction = $this->transactions->where('id', $gift['transaction_id'])->first()?->toArray();
        $treeIsd = json_decode($transaction['data'], true);
        $trees = $this->trees->whereIn('id', $treeIsd)->get()->toArray();
        $frozen = $this->getFrozenInfo($code);
        $this->gifts->getConnection()
            ->transaction(function () use ($gift, $transaction, $treeIsd, $code, $frozen,$trees,$user) {
                $this->gifts
                    ->where('code', $code)
                    ->update([
                        'email' => $user['email'],
                        'status' => 1,
                        'updated_at' => CDateTime::getCurrentDate(),
                    ]);
                $this->transactions
                    ->where('id', $transaction['id'])
                    ->update([
                        'status' => 1,
                        'updated_at' => CDateTime::getCurrentDate(),
                    ]);
                $this->trees
                    ->whereIn('id', $treeIsd)
                    ->update([
                        'user_id' => Auth::user()?->id,
                        'purchase_date' => CDateTime::getCurrentDate(),
                        'purchase_price' => 0,
                        'is_gifted' => 0,
                        'tree_gift_status_id' => 1,
                        'signed_documents' => 0,
                        'dividend_frozen_to' => $frozen['dividend_frozen_to'],
                        'sale_frozen_to' => $frozen['sale_frozen_to'],
                    ]);
                $orderId = $this->orders
                    ->insertGetId([
                        'user_id' => Auth::user()?->id,
                        'transaction_id' => $transaction['id'],
                        'status' => 1,
                        'trees_count' => $transaction['tree_count'],
                        'total' => 0,
                        'signed_documents' => 0,
                        'from_primary_market' => 0,
                        'created_at' => CDateTime::getCurrentDate(),
                        'updated_at' => CDateTime::getCurrentDate(),
                        'uuid' => Uuid::generate()->string,
                    ]);
                foreach ($trees as $tree) {
                    $this->orderDetails
                        ->insert([
                            'order_id' => $orderId,
                            'tree_id' => $tree['id'],
                            'price' => 0,
                            'planting_date' => $tree['planting_date'],
                            'purchase_date' => CDateTime::getCurrentDate(),
                            'tree_order_status_id' => 2,
                            'gift_status_id' => 4
                        ]);
                }
            });
    }

    public function getFrozenInfo(string $code): array
    {
        $result = [
            'dividend_frozen_to' => null,
            'sale_frozen_to' => null,
        ];
        $treeInfo = $this->gifts
            ->from($this->gifts->getTable(), 'g')
            ->leftJoin($this->giftedTrees->getTable() . ' as gt',
                'g.id',
                '=',
                'gt.gift_id',
            )
            ->where('g.code', $code)
            ->select([
                'gt.dividend_frozen_to',
                'gt.sale_frozen_to',
            ])
            ->first()
            ->toArray();
        if (!!$treeInfo['dividend_frozen_to']) {
            $result['dividend_frozen_to'] = CDateTime::getDateModified(
                CDateTime::getCurrentDate(),
                '+' . $treeInfo['dividend_frozen_to'] . 'year'
            );
        }
        if (!!$treeInfo['sale_frozen_to']) {
            $result['sale_frozen_to'] = CDateTime::getDateModified(
                CDateTime::getCurrentDate(),
                '+' . $treeInfo['sale_frozen_to'] . 'year'
            );
        }
        return $result;
    }
}
