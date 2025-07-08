<?php

namespace App\Filament\Resources\ArticleResource\Actions;

use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class ScheduleAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Schedule')
            ->icon('heroicon-o-calendar-days')
            ->visible(fn (Article $record) => ! $record->isPublished())
            ->form(fn (Article $article) => [
                DateTimePicker::make('published_at')
                    ->label('Published At')
                    ->displayFormat('M j, Y H:i')
                    ->seconds(false)
                    ->default($article->published_at)
                    ->required(),
            ])
            ->action(function (Article $article, array $data) {
                $article->publish(Carbon::parse($data['published_at']));
            })
            ->requiresConfirmation();
    }
}
