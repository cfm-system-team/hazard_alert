<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class RecipientRegister extends Mailable
{
    use Queueable, SerializesModels;

    protected $mail_data;

    /**
     * Create a new message instance.
     *
     * @param $mail_data
     */
    public function __construct($mail_data)
    {
        $this->mail_data = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'メールアドレス登録完了';
        if (Config::get('app.env') !== 'production') {
            $subject = '(TEST)' . $subject;
        }

        return $this->text('emails/recipient_register')
            ->subject($subject)->with($this->mail_data);
    }
}
