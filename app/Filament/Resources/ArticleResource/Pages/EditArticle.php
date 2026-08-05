<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Services\ArticleImageService;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Date;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    /**
     * When the hero image is replaced or removed, the stored crop selections
     * relate to the old image, so reset them to the default.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('hero_image', $data) && $data['hero_image'] !== $this->record->hero_image) {
            $data['og_image_crop'] = null;
            $data['card_image_crop'] = null;
            $data['header_image_crop'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Always regenerate the OG and card images on update to ensure they're current
        resolve(ArticleImageService::class)->refreshImages($this->record);
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
                    Radio::make('publish_type')
                        ->label('Publish Options')
                        ->options([
                            'now' => 'Publish Now',
                            'schedule' => 'Schedule for Later',
                        ])
                        ->default('now')
                        ->live()
                        ->required(),
                    DateTimePicker::make('published_at')
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
                        $this->record->publish(Date::parse($data['published_at']));
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
