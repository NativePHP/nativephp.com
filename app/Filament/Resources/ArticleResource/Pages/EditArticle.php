<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Services\OgImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function afterSave(): void
    {
        $service = app(OgImageService::class);

        // Always regenerate the OG image on update to ensure it's current
        $ogImageUrl = $service->generate($this->record);

        $this->record->update([
            'og_image' => $ogImageUrl,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
