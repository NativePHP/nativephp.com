<?php

namespace App\Filament\Resources\ArticleResource\Actions;

use App\Models\Article;
use Filament\Tables\Actions\Action;

class PublishAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Publish')
            ->icon('heroicon-o-newspaper')
            ->action(fn (Article $article) => $article->publish())
            ->visible(fn (Article $article) => ! $article->isPublished())
            ->requiresConfirmation();
    }
}
