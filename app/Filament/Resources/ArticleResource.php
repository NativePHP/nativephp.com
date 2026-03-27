<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Actions\PreviewAction;
use App\Filament\Resources\ArticleResource\Actions\PublishAction;
use App\Filament\Resources\ArticleResource\Actions\UnpublishAction;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'title';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Article $article, Set $set, ?string $state): void {
                        if ($article->isPublished()) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->unique(Article::class, 'slug', ignoreRecord: true)
                    ->disabled(fn (Article $article) => $article->isPublished())
                    ->afterStateUpdated(
                        fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                    )
                    ->helperText(fn (Article $article) => $article->isPublished()
                        ? 'The slug cannot be changed after the article is published.'
                        : false
                    ),

                Textarea::make('excerpt')
                    ->required()
                    ->maxLength(400)
                    ->columnSpanFull(),

                MarkdownEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->badge()
                    ->dateTime('M j, Y H:i')
                    ->color(fn ($state) => $state && $state->isPast() ? 'success' : 'warning')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("published_at IS NULL {$direction}, published_at {$direction}");
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    PreviewAction::make('preview'),
                    Actions\EditAction::make()->url(fn ($record) => static::getUrl('edit', ['record' => $record->id])),
                    UnpublishAction::make('unpublish'),
                    PublishAction::make('publish'),
                ]),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
