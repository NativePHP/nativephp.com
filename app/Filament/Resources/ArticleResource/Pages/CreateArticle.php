<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Services\OgImageService;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function afterCreate(): void
    {
        $service = resolve(OgImageService::class);

        $ogImageUrl = $service->generate($this->record);

        $this->record->update([
            'og_image' => $ogImageUrl,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record->id]);
    }
}
