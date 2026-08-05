<?php

namespace App\Jobs;

use App\Models\Plugin;
use App\Services\OgImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePluginOgImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Plugin $plugin) {}

    public function handle(OgImageService $ogImageService): void
    {
        $ogImageUrl = $ogImageService->generateForPlugin($this->plugin);

        $this->plugin->update([
            'og_image' => $ogImageUrl,
        ]);
    }
}
