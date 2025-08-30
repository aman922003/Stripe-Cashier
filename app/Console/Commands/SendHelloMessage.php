<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendHelloMessageJob; 
use Illuminate\Support\Facades\Log;

class SendHelloMessage extends Command 
{
    protected $signature = 'send:hello-message';
    protected $description = 'Send "Hello Dude how are you!" message to every user every minute';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $message = "Hello Dude how are you!";  

            // Dispatch the job to the queue
            SendHelloMessageJob::dispatch($message); 

            Log::info("Hello messages job dispatched successfully!");

            $this->info('Job dispatched to send hello messages!');

        } catch (\Throwable $t) {
            Log::error("Error in SendHelloMessage Command: " . $t->getMessage());
        }
    }
}
