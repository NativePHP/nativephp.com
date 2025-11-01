<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Colors\Rgb\Color;
use SimonHamp\TheOg\BorderPosition;
use SimonHamp\TheOg\Image;

class OgImageService
{
    /**
     * Generate an OG image for the given article.
     */
    public function generate(Article $article): string
    {
        $image = new Image;

        // Ensure the directory exists
        $directory = Storage::disk('public')->path('og-images');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $image
            ->layout(new NativePhpLayout)
            ->title($article->title)
            ->description($article->excerpt ?? '')
            ->backgroundColor('#ffffff')
            ->titleColor('#141624')
            ->descriptionColor('#141624')
            ->border(BorderPosition::All, new Color(80, 91, 147), 5)
            ->watermark(public_path('logo.svg'))
            ->url(url('/blog/'.$article->slug))
            ->save($this->getImagePath($article));

        return $this->getImageUrl($article);
    }

    /**
     * Delete the OG image for the given article.
     */
    public function delete(Article $article): bool
    {
        if ($article->og_image) {
            $path = str_replace('/storage/', '', parse_url($article->og_image, PHP_URL_PATH));

            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get the file path for storing the OG image.
     */
    protected function getImagePath(Article $article): string
    {
        return Storage::disk('public')->path("og-images/{$article->slug}.png");
    }

    /**
     * Get the public URL for the OG image.
     */
    protected function getImageUrl(Article $article): string
    {
        return Storage::disk('public')->url("og-images/{$article->slug}.png");
    }
}
