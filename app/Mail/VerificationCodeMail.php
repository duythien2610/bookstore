<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $userName;

    /**
     * Tạo instance mới.
     *
     * @param string $code   Mã OTP 6 số (plain text, chỉ gửi qua email)
     * @param string $userName Tên người dùng
     */
    public function __construct(string $code, string $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Mã xác thực tài khoản Modtra Books')
                    ->view('emails.verification-code');
    }
}
