<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageType;

    public function __construct($messageType)
    {
        $this->messageType = $messageType;
    }

    public function build()
    {
        return $this->view('emails.daily_message')
            ->with('messageType', $this->messageType);
    }
}
