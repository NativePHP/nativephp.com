<?php

namespace App\Jobs;

use App\Models\Plugin;
use App\Services\SatisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Rebuild a paid plugin in satis.
 *
 * Unique per plugin until it starts running, because pushes come in bursts and
 * one build picks up everything pushed before it runs: satis reads the repo
 * when it builds, not when the job was queued.
 */
class BuildPluginInSatis implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Seconds the lock is held at most, so a lost job can't block builds forever.
     */
    public int $uniqueFor = 600;

    /**
     * The plugin may be deleted while the build waits, which removes it from satis anyway.
     */
    public bool $deleteWhenMissingModels = true;

    public function __construct(public Plugin $plugin) {}

    public function uniqueId(): string
    {
        return (string) $this->plugin->id;
    }

    public function handle(SatisService $satisService): void
    {
        // The plugin may have gone free while the build waited
        if (! $this->plugin->isPaid()) {
            return;
        }

        $satisService->buildForPlugin($this->plugin);
    }
}
