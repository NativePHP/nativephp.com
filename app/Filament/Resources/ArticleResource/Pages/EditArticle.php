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
        $service = resolve(OgImageService::class);

        // Always regenerate the OG image on update to ensure it's current
        $ogImageUrl = $service->generate($this->record);

        $this->record->update([
            'og_image' => $ogImageUrl,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn () => route('article', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-newspaper')
                ->visible(fn () => ! $this->record->isPublished())
                ->form([
                    \Filament\Forms\Components\Radio::make('publish_type')
                        ->label('Publish Options')
                        ->options([
                            'now' => 'Publish Now',
                            'schedule' => 'Schedule for Later',
                        ])
                        ->default('now')
                        ->live()
                        ->required(),
                    \Filament\Forms\Components\DateTimePicker::make('published_at')
                        ->label('Published At')
                        ->displayFormat('M j, Y H:i')
                        ->seconds(false)
                        ->afterOrEqual('now')
                        ->visible(fn ($get) => $get('publish_type') === 'schedule')
                        ->required(fn ($get) => $get('publish_type') === 'schedule'),
                ])
                ->action(function (array $data): void {
                    if ($data['publish_type'] === 'now') {
                        $this->record->publish();
                    } else {
                        $this->record->publish(\Illuminate\Support\Facades\Date::parse($data['published_at']));
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
