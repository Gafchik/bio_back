<?php

namespace App\Console\Commands\GiftsNotification;

use App\Http\Classes\MailModels\NotificationGift\NotificationGiftMailModel;
use App\Http\Classes\Structure\CDateTime;
use App\Http\Classes\Structure\Lang;
use App\Models\MySql\Biodeposit\Gifts;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\LazyCollection;

class GiftsNotificationWorkerModel
{
    public function __construct(
        public Gifts $gifts,
    ){}
    public function getGifts(): LazyCollection
    {
        return $this->gifts
            ->where('status',0)
            ->where('is_notification',false)
            ->whereNotNull('notification_date')
            ->whereRaw("IFNULL(email,'')!= ''")
            ->cursor();
    }
    public function checkNotification(LazyCollection $gifts): void
    {
        $currentDate = CDateTime::getCurrentDate(CDateTime::DATE_FORMAT_DB);
        foreach ($gifts as $gift) {
            $notificationDate = CDateTime::convertDateToDateFormat($gift->notification_date,CDateTime::DATE_FORMAT_DB);
            if($notificationDate === $currentDate){
                try {
                    Mail::to($gift->email)
                        ->send(new NotificationGiftMailModel([
                            'email' => $gift->email,
                            'activationCode' => $gift->code,
                            'locale' => Lang::RUS
                        ]));
                }catch (\Exception $exception){}
                $this->gifts->where('id', $gift->id)
                    ->update([
                        'is_notification' => true,
                        'updated_at' => CDateTime::getCurrentDate(),
                    ]);
            }
        }

    }
}
