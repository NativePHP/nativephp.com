<?php

namespace App\Jobs;

use App\Models\PluginPayout;
use App\Services\StripeConnectService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPayoutTransfer implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public PluginPayout $payout) {}

    public function handle(StripeConnectService $stripeConnectService): void
    {
        $stripeConnectService->processTransfer($this->payout);
    }
}
