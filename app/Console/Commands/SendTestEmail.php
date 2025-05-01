<?php

namespace App\Console\Commands;

use App\Notifications\TestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendTestEmail extends Command
{
    protected $signature = 'app:send-test-email {email}';

    protected $description = 'Send a test email via the default mail driver.';

    public function handle(): void
    {
        $this->info('Sending test email via '.Mail::getDefaultDriver().'...');

        Notification::route('mail', $this->argument('email'))
            ->notify(new TestNotification);
    }
}
