<?php

namespace App\Http\Classes\MailModels\ActivationMail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class ActivationMailModel extends Mailable
{
    use Queueable, SerializesModels;
    private const VIEW_PATH = 'mailView.ActivationMail.ActivationMail';
    private const LANG_PATH = 'mailView/ActivationMail/ActivationMail';
    private array $data;
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->data['trans_prefix'] = self::LANG_PATH;
    }
    public function build(): Mailable
    {
        $subject = Lang::get(self::LANG_PATH.'.subject');
        return $this->subject($subject)
            ->view(self::VIEW_PATH, $this->data);
    }

}
