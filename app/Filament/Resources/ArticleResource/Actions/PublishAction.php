<?php

namespace App\Filament\Resources\ArticleResource\Actions;

use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class PublishAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Publish')
            ->icon('heroicon-o-newspaper')
            ->visible(fn (Article $article) => ! $article->isPublished())
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
            ->action(function (Article $article, array $data) {
                if ($data['publish_type'] === 'now') {
                    $article->publish();
                } else {
                    $article->publish(Carbon::parse($data['published_at']));
                }
            });
    }
}
