<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $donHang;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($donHang)
    {
        $this->donHang = $donHang;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Xác nhận đơn hàng #' . str_pad($this->donHang->id, 6, '0', STR_PAD_LEFT))
                    ->view('emails.order-confirmation');
    }
}
