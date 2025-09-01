<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Mail\DailyMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue; 
// use Illuminate\Foundation\Events\Dispatchable;

class SendHelloMessageJob implements ShouldQueue 
{
    use Queueable, SerializesModels, Dispatchable , InteractsWithQueue;

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
