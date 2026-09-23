<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Plugin;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Colors\Rgb\Color;
use SimonHamp\TheOg\BorderPosition;
use SimonHamp\TheOg\Image;
use SimonHamp\TheOg\Interfaces\Layout;

class OgImageService
{
    /**
     * Generate an OG image for the given article.
     */
    public function generate(Article $article): string
    {
        return $this->render(
            "og-images/{$article->slug}.png",
            $article->title,
            $article->excerpt ?? '',
            url('/blog/'.$article->slug),
        );
    }

    /**
     * Generate an OG image for the given plugin.
     */
    public function generateForPlugin(Plugin $plugin): string
    {
        return $this->render(
            "og-images/plugins/{$plugin->id}.png",
            $plugin->display_name ?? $plugin->name,
            $plugin->description ?? '',
            route('plugins.show', $plugin->routeParams()),
            new PluginOgLayout(
                version: $plugin->latest_version,
                mobileVersion: $plugin->mobile_min_version,
                iosVersion: $plugin->ios_version,
                androidVersion: $plugin->android_version,
            ),
        );
    }

    /**
     * Delete the OG image for the given article.
     */
    public function delete(Article $article): bool
    {
        return $this->deleteByUrl($article->og_image);
    }

    /**
     * Delete the OG image for the given plugin.
     */
    public function deleteForPlugin(Plugin $plugin): bool
    {
        return $this->deleteByUrl($plugin->og_image);
    }

    /**
     * Render an OG image to the public disk and return its public URL.
     */
    protected function render(string $path, string $title, string $description, string $url, ?Layout $layout = null): string
    {
        $fullPath = Storage::disk('public')->path($path);

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        (new Image)
            ->layout($layout ?? new NativePhpLayout)
            ->title($title)
            ->description($description)
            ->backgroundColor('#ffffff')
            ->titleColor('#141624')
            ->descriptionColor('#141624')
            ->border(BorderPosition::All, new Color(80, 91, 147), 5)
            ->watermark(public_path('logo.svg'))
            ->url($url)
            ->save($fullPath);

        return Storage::disk('public')->url($path);
    }

    /**
     * Delete a previously generated OG image given its public URL.
     */
    protected function deleteByUrl(?string $ogImageUrl): bool
    {
        if (! $ogImageUrl) {
            return false;
        }

        $path = str_replace('/storage/', '', parse_url($ogImageUrl, PHP_URL_PATH));

        return Storage::disk('public')->delete($path);
    }
}
