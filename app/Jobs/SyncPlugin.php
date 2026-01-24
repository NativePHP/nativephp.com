<?php

namespace App\Jobs;

use App\Models\Plugin;
use App\Services\PluginSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Plugin $plugin) {}

    public function handle(PluginSyncService $syncService): void
    {
        $syncService->sync($this->plugin);
    }
}
