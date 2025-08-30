<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\DailyMessageMail;
use Illuminate\Support\Facades\Mail;

class SendHelloMessage extends Command
{
    protected $signature = 'send:hello-message';
    protected $description = 'Send "Hello 5 Minute" message to every user every minute';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::all();  

        $message = "Hello Man!"; 

        // Loop through each user and send the email
        foreach ($users as $index => $user) {
            // $delayInSeconds = $index * 5; 

            // Mail::to($user->email)
            //     ->later(now()->addSeconds($delayInSeconds), new DailyMessageMail($message));
            Mail::to($user->email)->send(new DailyMessageMail($message));
        }

        $this->info('Hello messages sent successfully!');
    }
}
