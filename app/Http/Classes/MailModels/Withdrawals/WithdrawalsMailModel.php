<?php

namespace App\Http\Classes\MailModels\Withdrawals;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class WithdrawalsMailModel extends Mailable
{
    use Queueable, SerializesModels;
    private const VIEW_PATH = 'mailView.Withdrawals.Withdrawals';
    private const LANG_PATH = 'mailView/Withdrawals/Withdrawals';
    private array $data;
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->data['trans_prefix'] = self::LANG_PATH;
    }
    public function build(): Mailable
    {
        return $this->subject('Withdrawal request')
            ->view(self::VIEW_PATH, $this->data);
    }

}
