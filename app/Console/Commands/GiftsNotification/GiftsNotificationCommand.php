<?php

namespace App\Console\Commands\GiftsNotification;

use Illuminate\Console\Command;
use Throwable;

class GiftsNotificationCommand extends Command
{
    private const COMMAND_NAME = 'GIFTS_NOTIFICATION_COMMAND';

    public function __construct(
        private GiftsNotificationWorker $worker
    )
    {
        parent::__construct();
    }

    protected $signature = "workers:check-gifts-notification";

    protected $description = "check gifts notification";

    public function handle()
    {
        try {
            $this->worker->startWork();
        } catch (Throwable $e) {
            return self::FAILURE;
        }
    }
}
