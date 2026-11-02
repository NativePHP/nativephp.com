<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use RuntimeException;

class AdminArticleMediaService
{
    public const DISK = 'public';

    /** Filament article hero/featured uploads. */
    public const HERO_DIRECTORY = 'blog/heroes';

    /** Default directory for generic MCP media uploads when none is provided. */
    public const DEFAULT_DIRECTORY = 'website-images';

    /** Hard cap on decoded upload bytes (a few MB). */
    public const MAX_DECODED_BYTES = 5 * 1024 * 1024;

    /** Soft target for re-encoded WebP heroes. */
    public const WEBP_QUALITY = 82;

    /**
     * @var list<string>
     */
    public const ALLOWED_CONTENT_TYPES = [
        'image/webp',
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    public function __construct(protected ArticleImageService $articleImageService) {}

    /**
     * Store an image from raw binary onto the public disk under a sanitized directory.
     *
     * @return array{
     *     path: string,
     *     media_id: string,
     *     url: string,
     *     content_type: string,
     *     bytes: int,
     *     width: int,
     *     height: int,
     *     original_filename: string|null,
     *     directory: string
     * }
     */
    public function storeHeroFromBinary(string $binary, string $contentType, ?string $filename = null, ?string $directory = null): array
    {
        $contentType = $this->normalizeContentType($contentType);
        $directory = $this->sanitizeDirectory($directory);

        if (! in_array($contentType, self::ALLOWED_CONTENT_TYPES, true)) {
            throw new RuntimeException('contentType must be image/webp, image/jpeg, or image/png.');
        }

        $size = strlen($binary);

        if ($size === 0) {
            throw new RuntimeException('Image content is empty.');
        }

        if ($size > self::MAX_DECODED_BYTES) {
            throw new RuntimeException('Image exceeds the '.self::MAX_DECODED_BYTES.' byte decoded size limit.');
        }

        try {
            $image = ImageManager::gd()->read($binary);
        } catch (\Throwable $e) {
            throw new RuntimeException('Could not decode image content: '.$e->getMessage(), previous: $e);
        }

        $width = $image->width();
        $height = $image->height();

        if ($width < ArticleImageService::OG_WIDTH || $height < ArticleImageService::OG_HEIGHT) {
            throw new RuntimeException(
                'Image must be at least '.ArticleImageService::OG_WIDTH.'×'.ArticleImageService::OG_HEIGHT.'px (got '.$width.'×'.$height.').'
            );
        }

        if ($width > ArticleImageService::HERO_MAX_WIDTH) {
            $image->scaleDown(width: ArticleImageService::HERO_MAX_WIDTH);
            $width = $image->width();
            $height = $image->height();
        }

        // Prefer WebP for MCP uploads (smaller for CDN).
        $encoded = $image->toWebp(self::WEBP_QUALITY);
        $encodedBinary = $encoded->toString();

        if (strlen($encodedBinary) > self::MAX_DECODED_BYTES) {
            throw new RuntimeException('Encoded WebP still exceeds the size limit; provide a smaller source image.');
        }

        $disk = Storage::disk(self::DISK);
        $disk->makeDirectory($directory);

        $basename = Str::uuid()->toString().'.webp';
        $path = $directory.'/'.$basename;

        $disk->put($path, $encodedBinary, 'public');

        return [
            'path' => $path,
            'media_id' => $path,
            'url' => $this->publicUrl($path),
            'content_type' => 'image/webp',
            'bytes' => strlen($encodedBinary),
            'width' => $width,
            'height' => $height,
            'original_filename' => $filename,
            'directory' => $directory,
        ];
    }

    /**
     * Decode base64 (no data: prefix) and store under the given public-disk directory.
     *
     * @return array{
     *     path: string,
     *     media_id: string,
     *     url: string,
     *     content_type: string,
     *     bytes: int,
     *     width: int,
     *     height: int,
     *     original_filename: string|null,
     *     directory: string
     * }
     */
    public function storeHeroFromBase64(string $base64, string $contentType, ?string $filename = null, ?string $directory = null): array
    {
        $base64 = trim($base64);

        if (str_starts_with($base64, 'data:')) {
            throw new RuntimeException('content must be raw base64 without a data: URI prefix.');
        }

        // Normalize before strict decode:
        // 1) strip whitespace (newlines/tabs/wrapping), 2) recover '+' mangled to spaces,
        // 3) if -/_ present, accept base64url via strtr to the standard alphabet.
        // Spaces are preserved through the whitespace strip so they can be restored to '+'.
        $base64 = preg_replace('/[\t\n\r\f\v]+/', '', $base64) ?? $base64;
        $base64 = str_replace(' ', '+', $base64);

        if (str_contains($base64, '-') || str_contains($base64, '_')) {
            $base64 = strtr($base64, '-_', '+/');
        }

        // Reject before decode so oversized payloads cannot exhaust memory.
        // Base64 expands 3 bytes → 4 chars; floor(len*3/4) is a safe decoded upper bound.
        if ((int) floor(strlen($base64) * 3 / 4) > self::MAX_DECODED_BYTES) {
            throw new RuntimeException('Image exceeds the '.self::MAX_DECODED_BYTES.' byte decoded size limit.');
        }

        $binary = base64_decode($base64, true);

        if ($binary === false) {
            throw new RuntimeException('content is not valid base64. Transport may have mangled + into spaces — pass content as a JSON string argument (raw base64, no data: prefix), not freeform prose.');
        }

        return $this->storeHeroFromBinary($binary, $contentType, $filename, $directory);
    }

    /**
     * Download an image from a public URL and store it as a hero.
     *
     * @return array{
     *     path: string,
     *     media_id: string,
     *     url: string,
     *     content_type: string,
     *     bytes: int,
     *     width: int,
     *     height: int,
     *     original_filename: string|null
     * }
     */
    public function storeHeroFromUrl(string $url): array
    {
        $url = trim($url);

        if (! preg_match('#^https?://#i', $url)) {
            throw new RuntimeException('hero_image_url must be an http(s) URL.');
        }

        // If the URL already points at our public storage heroes path, reuse the file.
        if ($existing = $this->pathFromPublicUrl($url)) {
            if (! Storage::disk(self::DISK)->exists($existing)) {
                throw new RuntimeException('Referenced media path does not exist on the public disk.');
            }

            if (! str_starts_with($existing, self::HERO_DIRECTORY.'/')) {
                throw new RuntimeException('media_id/path must be under '.self::HERO_DIRECTORY.'.');
            }

            return $this->describeExisting($existing);
        }

        $response = Http::timeout(30)
            ->withHeaders(['Accept' => 'image/webp,image/jpeg,image/png,*/*'])
            ->withOptions(['allow_redirects' => ['max' => 3]])
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to download hero_image_url (HTTP '.$response->status().').');
        }

        $binary = $response->body();
        $contentType = $response->header('Content-Type') ?: 'image/jpeg';
        $contentType = strtok($contentType, ';') ?: $contentType;

        $filename = basename(parse_url($url, PHP_URL_PATH) ?: 'hero');

        return $this->storeHeroFromBinary($binary, $contentType, $filename, self::HERO_DIRECTORY);
    }

    /**
     * Resolve a media_id / path that already lives on the public disk.
     *
     * @return array{
     *     path: string,
     *     media_id: string,
     *     url: string,
     *     content_type: string,
     *     bytes: int,
     *     width: int,
     *     height: int,
     *     original_filename: string|null
     * }
     */
    public function resolveExistingMedia(string $mediaIdOrPath): array
    {
        $path = ltrim(trim($mediaIdOrPath), '/');

        if ($fromUrl = $this->pathFromPublicUrl($path)) {
            $path = $fromUrl;
        }

        if (! str_starts_with($path, self::HERO_DIRECTORY.'/')) {
            throw new RuntimeException('media_id/path must be under '.self::HERO_DIRECTORY.'.');
        }

        if (! Storage::disk(self::DISK)->exists($path)) {
            throw new RuntimeException('Referenced media path does not exist on the public disk.');
        }

        return $this->describeExisting($path);
    }

    /**
     * Attach a stored hero path to an article and regenerate derived images.
     */
    public function attachHeroToArticle(Article $article, string $path): Article
    {
        if (! str_starts_with($path, self::HERO_DIRECTORY.'/')) {
            throw new RuntimeException('Hero path must be under '.self::HERO_DIRECTORY.'.');
        }

        if (! Storage::disk(self::DISK)->exists($path)) {
            throw new RuntimeException('Hero path does not exist on the public disk.');
        }

        $article->fill([
            'hero_image' => $path,
            'og_image_crop' => null,
            'card_image_crop' => null,
            'header_image_crop' => null,
        ]);
        $article->save();

        $this->articleImageService->refreshImages($article->fresh());

        return $article->fresh();
    }

    public function publicUrl(string $path): string
    {
        return Storage::disk(self::DISK)->url($path);
    }

    /**
     * @return array{
     *     path: string,
     *     media_id: string,
     *     url: string,
     *     content_type: string,
     *     bytes: int,
     *     width: int,
     *     height: int,
     *     original_filename: string|null
     * }
     */
    protected function describeExisting(string $path): array
    {
        $binary = Storage::disk(self::DISK)->get($path);
        $image = ImageManager::gd()->read($binary);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream',
        };

        return [
            'path' => $path,
            'media_id' => $path,
            'url' => $this->publicUrl($path),
            'content_type' => $contentType,
            'bytes' => strlen($binary),
            'width' => $image->width(),
            'height' => $image->height(),
            'original_filename' => basename($path),
        ];
    }

    /**
     * Sanitize a relative public-disk directory path.
     *
     * Rejects absolute paths and ".." segments. Nested paths like blog/heroes are allowed.
     * When null/blank, returns DEFAULT_DIRECTORY (website-images).
     */
    public function sanitizeDirectory(?string $directory): string
    {
        if ($directory === null || trim($directory) === '') {
            return self::DEFAULT_DIRECTORY;
        }

        $directory = str_replace('\\', '/', trim($directory));

        if (str_starts_with($directory, '/') || preg_match('#^[A-Za-z]:/#', $directory)) {
            throw new RuntimeException('directory must be a relative path under the public media disk (no absolute paths).');
        }

        if (str_contains($directory, '..')) {
            throw new RuntimeException('directory must not contain "..".');
        }

        $directory = trim(preg_replace('#/+#', '/', $directory) ?? $directory, '/');

        if ($directory === '') {
            return self::DEFAULT_DIRECTORY;
        }

        if (! preg_match('#^[A-Za-z0-9][A-Za-z0-9/_-]*$#', $directory)) {
            throw new RuntimeException('directory may only contain letters, numbers, hyphens, underscores, and slashes.');
        }

        return $directory;
    }

    protected function normalizeContentType(string $contentType): string
    {
        $contentType = strtolower(trim(strtok($contentType, ';') ?: $contentType));

        return $contentType === 'image/jpg' ? 'image/jpeg' : $contentType;
    }

    protected function pathFromPublicUrl(string $url): ?string
    {
        $trimmed = ltrim(trim($url), '/');

        if (str_starts_with($trimmed, self::HERO_DIRECTORY.'/')) {
            return $trimmed;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return null;
        }

        if (preg_match('~/storage/('.preg_quote(self::HERO_DIRECTORY, '~').'/[^?#]+)$~', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
