<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AirQualityWarning extends Mailable
{
    use Queueable, SerializesModels;

    public $userInfo;

    /**
     * Create a new message instance.
     *
     * @param array $userInfo 用户信息
     * @return void
     */
    public function __construct(array $userInfo)
    {
        $this->userInfo = $userInfo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('UioSun@163.com')
            ->view('emails.other.airQuality');
    }
}
