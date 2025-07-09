<?php

namespace App\Filament\Resources\ArticleResource\Actions;

use App\Models\Article;
use Filament\Tables\Actions\Action;

class PreviewAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Preview')
            ->icon('heroicon-o-eye')
            ->url(fn (Article $article) => route('article', $article))
            ->openUrlInNewTab();
    }
}
