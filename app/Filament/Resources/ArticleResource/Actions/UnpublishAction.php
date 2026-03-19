<?php

namespace App\Filament\Resources\ArticleResource\Actions;

use App\Models\Article;
use Filament\Actions\Action;

class UnpublishAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Unpublish')
            ->icon('heroicon-o-archive-box-x-mark')
            ->action(fn (Article $article) => $article->unpublish())
            ->visible(fn (Article $article) => $article->isPublished())
            ->requiresConfirmation();
    }
}
