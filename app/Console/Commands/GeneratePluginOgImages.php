<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePluginOgImage;
use App\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GeneratePluginOgImages extends Command
{
    protected $signature = 'plugins:generate-og-images
        {--missing : Only generate images that do not already exist on disk}';

    protected $description = 'Generate (or regenerate) the OpenGraph images for plugins';

    public function handle(): int
    {
        $plugins = Plugin::query()->get();

        if ($this->option('missing')) {
            $plugins = $plugins->reject(
                fn (Plugin $plugin) => Storage::disk('public')->exists("og-images/plugins/{$plugin->id}.png")
            );
        }

        if ($plugins->isEmpty()) {
            $this->info('No plugins need OG images.');

            return self::SUCCESS;
        }

        $this->withProgressBar($plugins, function (Plugin $plugin): void {
            GeneratePluginOgImage::dispatchSync($plugin);
        });

        $this->newLine(2);
        $this->info("Generated OG images for {$plugins->count()} plugin(s).");

        return self::SUCCESS;
    }
}
