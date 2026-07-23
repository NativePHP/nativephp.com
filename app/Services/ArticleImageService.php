<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ArticleImageService
{
    public const OG_WIDTH = 1200;

    public const OG_HEIGHT = 630;

    public const CARD_WIDTH = 1200;

    public const CARD_HEIGHT = 450;

    public const HEADER_WIDTH = 2048;

    public const HEADER_HEIGHT = 1000;

    public const HERO_MAX_WIDTH = 2400;

    public function __construct(protected OgImageService $ogImageService) {}

    /**
     * Regenerate the article's OG and card images.
     *
     * When a hero image has been uploaded, the OG and card images are cropped
     * from it using the stored crop selections. Otherwise the OG image is
     * generated with TheOG and any card image is removed.
     */
    public function refreshImages(Article $article): void
    {
        if ($article->hero_image && Storage::disk('public')->exists($article->hero_image)) {
            $this->scaleDownHero($article);

            $article->update([
                'og_image' => $this->generateCrop(
                    $article,
                    $article->og_image_crop,
                    self::OG_WIDTH,
                    self::OG_HEIGHT,
                    'og-images/'.$article->slug.'.png',
                ),
                'card_image' => $this->generateCrop(
                    $article,
                    $article->card_image_crop,
                    self::CARD_WIDTH,
                    self::CARD_HEIGHT,
                    'blog/cards/'.$article->slug.'.jpg',
                ),
                'header_image' => $this->generateCrop(
                    $article,
                    $article->header_image_crop,
                    self::HEADER_WIDTH,
                    self::HEADER_HEIGHT,
                    'blog/headers/'.$article->slug.'.jpg',
                ),
            ]);

            return;
        }

        $this->deleteDerivedImages($article);

        $article->update([
            'og_image' => $this->ogImageService->generate($article),
            'card_image' => null,
            'header_image' => null,
        ]);
    }

    /**
     * Delete image files that are no longer referenced after an update.
     */
    public function pruneStaleImages(Article $article): void
    {
        $disk = Storage::disk('public');

        if ($article->wasChanged('hero_image') && ($previousHero = $article->getOriginal('hero_image'))) {
            $disk->delete($previousHero);
        }

        if ($article->wasChanged('slug') && ($previousSlug = $article->getOriginal('slug'))) {
            $disk->delete([
                'og-images/'.$previousSlug.'.png',
                'blog/cards/'.$previousSlug.'.jpg',
                'blog/headers/'.$previousSlug.'.jpg',
            ]);
        }
    }

    /**
     * Delete all image files belonging to the article.
     */
    public function deleteImages(Article $article): void
    {
        if ($article->hero_image) {
            Storage::disk('public')->delete($article->hero_image);
        }

        $this->deleteDerivedImages($article);

        $this->ogImageService->delete($article);
    }

    /**
     * Scale the hero image down in place if it's larger than we'd ever render it.
     */
    protected function scaleDownHero(Article $article): void
    {
        $hero = ImageManager::gd()->read(Storage::disk('public')->path($article->hero_image));

        if ($hero->width() > self::HERO_MAX_WIDTH) {
            $hero->scaleDown(width: self::HERO_MAX_WIDTH)->save();
        }
    }

    /**
     * Crop a region out of the hero image and save it at the target size.
     *
     * @param  array{x: int, y: int, width: int, height: int}|null  $crop
     */
    protected function generateCrop(Article $article, ?array $crop, int $targetWidth, int $targetHeight, string $path): string
    {
        $disk = Storage::disk('public');

        $directory = dirname($path);
        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $hero = ImageManager::gd()->read($disk->path($article->hero_image));

        $rect = $this->resolveCropRect($crop, $hero->width(), $hero->height(), $targetWidth / $targetHeight);

        $options = str_ends_with($path, '.png') ? [] : ['quality' => 90];

        $hero
            ->crop($rect['width'], $rect['height'], $rect['x'], $rect['y'])
            ->resize($targetWidth, $targetHeight)
            ->blendTransparency('ffffff')
            ->save($disk->path($path), ...$options);

        return $disk->url($path);
    }

    /**
     * Clamp a stored crop selection to the image bounds, or fall back to a
     * centered crop of the largest possible area at the target aspect ratio.
     *
     * @return array{x: int, y: int, width: int, height: int}
     */
    protected function resolveCropRect(?array $crop, int $imageWidth, int $imageHeight, float $ratio): array
    {
        if ($this->isUsableCropRect($crop)) {
            $width = min(max((int) $crop['width'], 10), $imageWidth);
            $height = (int) round($width / $ratio);

            if ($height > $imageHeight) {
                $height = $imageHeight;
                $width = (int) round($height * $ratio);
            }

            return [
                'x' => min(max((int) $crop['x'], 0), $imageWidth - $width),
                'y' => min(max((int) $crop['y'], 0), $imageHeight - $height),
                'width' => $width,
                'height' => $height,
            ];
        }

        $width = min($imageWidth, (int) floor($imageHeight * $ratio));
        $height = (int) floor($width / $ratio);

        return [
            'x' => (int) floor(($imageWidth - $width) / 2),
            'y' => (int) floor(($imageHeight - $height) / 2),
            'width' => $width,
            'height' => $height,
        ];
    }

    protected function isUsableCropRect(?array $crop): bool
    {
        return is_array($crop)
            && isset($crop['x'], $crop['y'], $crop['width'], $crop['height'])
            && is_numeric($crop['x'])
            && is_numeric($crop['y'])
            && is_numeric($crop['width'])
            && is_numeric($crop['height'])
            && (int) $crop['width'] > 0
            && (int) $crop['height'] > 0;
    }

    protected function deleteDerivedImages(Article $article): void
    {
        Storage::disk('public')->delete([
            'blog/cards/'.$article->slug.'.jpg',
            'blog/headers/'.$article->slug.'.jpg',
        ]);
    }
}
