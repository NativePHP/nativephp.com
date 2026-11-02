<?php

namespace App\Jobs;

use App\Services\SatisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Drop a package from satis.
 *
 * Takes the package name rather than the Plugin so it can still run once the
 * plugin row is gone.
 */
class RemovePluginFromSatis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $packageName) {}

    public function handle(SatisService $satisService): void
    {
        $satisService->removePackage($this->packageName);
    }
}
