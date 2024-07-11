<?php

namespace App\Http\Classes\LogicalModels\Auth;

use App\Http\Classes\LogicalModels\Auth\Exceptions\ActivationsEmailNotFoundException;
use App\Http\Classes\LogicalModels\Auth\Exceptions\CheckPasswordCodeException;
use App\Http\Classes\LogicalModels\Auth\Exceptions\IncorrectCodeException;
use App\Http\Classes\MailModels\ActivationMail\ForgotPasswordMailModel;
use App\Http\Classes\Traits\CacheTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use PragmaRX\Google2FAQRCode\Google2FA;

class Auth
{
    private const CHANGE_PASSWORD_KEY = 'change_password_key';
    private const TTL_PASSWORD_CODE = 300;
    private Google2FA $google2fa;
    use CacheTrait;
    public function __construct(
        private AuthModel $model,
    ){
        $this->google2fa = new Google2FA();
    }
    public function checkEmail(array $data): bool
    {
        return $this->model->checkEmail($data);
    }
    public function reg(array $data): bool
    {
        return $this->model->reg($data);
    }
    public function emailActivate(array $data): void
    {
        $activationData = $this->model->getActivationData($data['email']);
        if(empty($activationData)){
            throw new ActivationsEmailNotFoundException();
        }
        if($activationData['code'] != $data['code']){
            throw new IncorrectCodeException();
        }
        $this->model->completeActivation($activationData['id']);
    }
    public function forgotPasswordSendCode(array $data): void
    {
        $email = $data['email'];
        if(!$this->model->checkEmail($data)){
            throw new ActivationsEmailNotFoundException();
        }
        if($this->hasInCache(self::CHANGE_PASSWORD_KEY.$email)){
            $cacheData = $this->getFromCache(self::CHANGE_PASSWORD_KEY.$email);
        }else{
            $cacheData = [
                'code' => $this->model->createActivationEmailCode(),
                'email' => $email
            ];
            $this->putToCache(
                self::CHANGE_PASSWORD_KEY.$email,
                $cacheData,
                self::TTL_PASSWORD_CODE
            );
        }
        Mail::to($email)
            ->send(new ForgotPasswordMailModel([
                'email' => $data['email'],
                'activationCode' => $cacheData['code'],
                'locale' => app()->getLocale(),
            ]));
    }
    public function checkForgotCode(array $data): void
    {
        $email = $data['email'];
        if(!$this->hasInCache(self::CHANGE_PASSWORD_KEY.$email)){
            throw new CheckPasswordCodeException();
        }
        $cacheData = $this->getFromCache(self::CHANGE_PASSWORD_KEY.$email);
        if($cacheData['code'] != $data['code']){
            throw new CheckPasswordCodeException();
        }
    }
    public function changePassword(array $data): void
    {
        $email = $data['email'];
        if(!$this->hasInCache(self::CHANGE_PASSWORD_KEY.$email)){
            throw new CheckPasswordCodeException();
        }
        $cacheData = $this->getFromCache(self::CHANGE_PASSWORD_KEY.$email);
        if($cacheData['code'] != $data['code']){
            throw new CheckPasswordCodeException();
        }
        $this->model->changePassword($data);
    }
    public function getUserInfo(string $email): ?array
    {
        $id = $this->model->getUserId($email);
        if(empty($id)){
            return null;
        }
        return $this->model->getUserInfo($id);
    }
    public function checkHas2Fa(): array
    {
        $user = $this->getUserInfo(FacadesAuth::user()?->email);
        if(!!$user['enable_2_fact']){
            return [
                'enable_2_fact' => true,
            ];
        }
        if(!empty($user['secret_key'])){
            $code2fa = decrypt($user['secret_key']);
        }else{
            $code2fa = $this->google2fa->generateSecretKey();
            $this->model->set2fac($user['id'],encrypt($code2fa));
        }
        $QR_Image =$this->google2fa->getQRCodeInline(
            config('app.name'),
            $user['email'],
            $code2fa
        );
        return [
            'enable_2_fact' => !!$user['enable_2_fact'],
            'key' => $code2fa,
            'qr' => $QR_Image,
        ];
    }
    public function enable2Fa(array $data): void
    {
        $user = $this->getUserInfo(FacadesAuth::user()?->email);
        $code = $data['code'];
        $secretKey = decrypt($user['secret_key']);
        $isValidKey = $this->google2fa->verifyKey($secretKey,$code);
        if(!$isValidKey){
            throw new IncorrectCodeException();
        }
        $this->model->change2fac(true);
    }
    public function disable2Fa(array $data): void
    {
        $user = $this->getUserInfo(FacadesAuth::user()?->email);
        $code = $data['code'];
        $secretKey = decrypt($user['secret_key']);
        $isValidKey = $this->google2fa->verifyKey($secretKey,$code);
        if(!$isValidKey){
            throw new IncorrectCodeException();
        }
        $this->model->change2fac(false);
    }
}
