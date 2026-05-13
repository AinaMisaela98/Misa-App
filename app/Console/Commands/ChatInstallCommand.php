<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:chat-install-command')]
#[Description('Command description')]
class ChatInstallCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info("Chat installer running...");
    }
}
