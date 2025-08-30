<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Mail\DailyMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue; 
use Illuminate\Foundation\Events\Dispatchable;

class SendHelloMessageJob implements ShouldQueue 
{
    use Queueable, SerializesModels, Dispatchable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        $users = User::all();  

        foreach ($users as $user) {
            Mail::to($user->email)->send(new DailyMessageMail($this->message));
        }
    }
}
