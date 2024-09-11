<?php

namespace App\Console\Commands\GiftsNotification;

class GiftsNotificationWorker
{
    public function __construct(
        private GiftsNotificationWorkerModel $model
    ){}

    public function startWork()
    {
        try {
            $gifts = $this->model->getGifts();
            $this->model->checkNotification($gifts);
        }catch (\Exception $e){dd($e->getMessage());}

    }
}
