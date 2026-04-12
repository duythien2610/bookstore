<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\DonHang;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $donHang;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DonHang $donHang)
    {
        $this->donHang = $donHang;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->donHang->user && $this->donHang->user->email) {
            Mail::to($this->donHang->user->email)->send(new OrderConfirmationMail($this->donHang));
        }
    }
}
