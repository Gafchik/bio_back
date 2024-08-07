<?php

namespace App\Http\Classes\LogicalModels\Auth;

use App\Http\Classes\Helpers\PasswordHashHelper;
use App\Http\Classes\MailModels\ActivationMail\ActivationMailModel;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\CustomHeaders;
use App\Http\Classes\Structure\Lang;
use App\Http\Classes\Structure\WalletsType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\MySql\Biodeposit\{
    Users,
    UserInfo,
    User_setting,
    Wallets,
    Activations,
    Trees,
};
use Webpatser\Uuid\Uuid;

class AuthModel
{
    public function __construct(
        private Users $users,
        private UserInfo $userInfo,
        private User_setting $userSetting,
        private Wallets $wallets,
        private Activations $activationsEmail,
        private Trees $trees,
    ){}

    public function checkEmail(array $data): bool
    {
        return $this->users->where('email', $data['email'])->exists();
    }
    public function reg(array $data): bool
    {
        $this->users->getConnection()
            ->transaction(function () use ($data) {
                $userId = $this->insertInToUsers($data);
                $this->insertInToUserInfo($userId,$data);
                $this->insertInToUserSettings($userId,$data);
                $this->createNewUserWallets($userId);
                $this->createActivationsEmail($userId,$data);
            });
        return true;
    }
    private function insertInToUsers(array $data): int
    {
        $usersData = [
            'uuid' => Uuid::generate()->string,
            'email' => $data['email'],
            'bad_email' => 0,
            'password' => PasswordHashHelper::generatePasswordHash($data['password']),
            'referral_link' => $this->generateReferralLink(),
            'created_at' => CDateTime::getCurrentDate(),
            'updated_at' => CDateTime::getCurrentDate(),
            'enable_2_fact' => 0,
        ];
        return $this->users->insertGetId($usersData);
    }
    private function generateReferralLink(): string
    {
        $dateTime = CDateTime::getCurrentDate(CDateTime::DATE_TIME_FROM_REFERRAL_LINK);
        return substr(md5($dateTime), 10, 8);
    }
    private function insertInToUserInfo(int $id, array $data): void
    {
        $this->userInfo->insert([
            'user_id' => $id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'level' => 0,
        ]);
    }
    private function insertInToUserSettings(int $id, array $data): void
    {
        $lang = Lang::RUS;
        $headerLang = request()->header(CustomHeaders::LANG_HEADER);
        if(!is_null($headerLang)){
            $headerLang =  mb_strtolower($headerLang);
            if(in_array($headerLang,Lang::ARRAY_LANG)) {
                $lang = $headerLang;
            }
        }
        $this->userSetting->insert([
            'user_id' => $id,
            'locale' => $lang,
            'promocode_discount' => 0,
            'promocode_bonus' => 0,
            'promocode_wallet' => WalletsType::BONUS,
            'promocode_multiple' => 1,
            'show_popup' => 0,
        ]);
    }
    public function createNewUserWallets(int $id): void
    {
        foreach (WalletsType::WALLETS_TYPES as $type){
            $this->wallets->insert([
                'user_id' => $id,
                'type' => $type,
                'balance' => 0,
                'created_at' => CDateTime::getCurrentDate(),
                'updated_at' => CDateTime::getCurrentDate(),
            ]);
        }
    }
    public function createActivationsEmail(int $id,array $data): void
    {
        $code = $this->createActivationEmailCode();
        $this->activationsEmail->insert([
            'user_id' => $id,
            'code' => $code,
            'completed' => 0,
            'created_at' => CDateTime::getCurrentDate(),
            'updated_at' => CDateTime::getCurrentDate(),
        ]);
        Mail::to($data['email'])
            ->send(new ActivationMailModel([
                'email' => $data['email'],
                'activationCode' => $code,
                'locale' => app()->getLocale(),
            ]));
    }

    public function createActivationEmailCode(): int
    {
        return rand(100000, 999999);
    }
    public function getActivationData(string $email): ?array
    {
        return $this->users
            ->from($this->users->getTable(), 'users')
            ->leftJoin($this->activationsEmail->getTable() . ' as activationsEmail',
                'activationsEmail.user_id',
                '=',
                'users.id',
            )
            ->where('users.email', $email)
            ->where('activationsEmail.completed', 0)
            ->first()
            ?->toArray();
    }
    public function completeActivation(int $id): void
    {
        $this->activationsEmail
            ->where('id', $id)
            ->update([
                'completed' => true,
            ]);
    }
    public function changePassword(array $data): void
    {
        $this->users
            ->where('email', $data['email'])
            ->update([
                'password' => PasswordHashHelper::generatePasswordHash($data['password']),
            ]);
    }
    public function getUserInfo(int $id): ?array
    {
        $result = $this->users
            ->from($this->users->getTable(). ' as userModel')
            ->leftJoin($this->userInfo->getTable() . ' as userInfo',
                'userModel.id',
                '=',
                'userInfo.user_id'
            )
            ->leftJoin($this->userSetting->getTable() . ' as userSetting',
                'userModel.id',
                '=',
                'userSetting.user_id'
            )
            ->where('userModel.id',$id)
            ->select([
                'userModel.id',
                'userModel.email',
                'userSetting.locale',
                'userModel.permissions',
                'userModel.is_active_user',
                'userInfo.first_name',
                'userInfo.last_name',
                'userInfo.phone',
                'userSetting.locale',
                'userSetting.promocode',
                'userInfo.level',
                'userModel.google2fa_secret as secret_key',
                'userModel.enable_2_fact',
            ])
            ->selectRaw('!ISNULL(userModel.google2fa_secret) as has_2fa_code')
            ->first()
            ?->toArray();
        if(!empty($result)){
            $result = [
              ...$result,
              'wallets' => $this->getWallets($id),
              'count_trees' => $this->getCountTrees($id),
            ];
        }
        return $result;
    }
    public function getUserId(string $email): ?int
    {
        return $this->users->where('email',$email)->pluck('id')->first();
    }
    private function getWallets(int $id): array
    {
        return $this->wallets
            ->where('user_id', $id)
            ->get([
                'type',
                'balance',
            ])
            ->toArray();
    }
    private function getCountTrees(int $id): int
    {
        return $this->trees
            ->where('user_id',$id)
            ->count();
    }
    public function set2fac(int $userId,string $code2fa): void
    {
        $this->users
            ->where('id',$userId)
            ->update([
                'google2fa_secret' => $code2fa
            ]);
    }
    public function change2fac(bool $isEnable)
    {
        $this->users
            ->where('id',Auth::user()?->id)
            ->update([
                'enable_2_fact' => $isEnable
            ]);
    }
}
