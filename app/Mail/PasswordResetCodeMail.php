<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $userName;

    public function __construct(string $code, string $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Đặt lại mật khẩu — Modtra Books')
                    ->view('emails.password-reset-code');
    }
}
