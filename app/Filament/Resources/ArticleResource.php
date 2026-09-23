<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\ImageCropper;
use App\Filament\Resources\ArticleResource\Actions\PreviewAction;
use App\Filament\Resources\ArticleResource\Actions\PublishAction;
use App\Filament\Resources\ArticleResource\Actions\UnpublishAction;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Services\ArticleImageService;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'title';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $breadcrumb = 'Blog';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?Article $article, Set $set, ?string $state): void {
                        if ($article?->isPublished()) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->unique(Article::class, 'slug', ignoreRecord: true)
                    ->disabled(fn (?Article $article) => $article?->isPublished() ?? false)
                    ->afterStateUpdated(
                        fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                    )
                    ->helperText(fn (?Article $article) => $article?->isPublished()
                        ? 'The slug cannot be changed after the article is published.'
                        : false
                    ),

                Textarea::make('excerpt')
                    ->required()
                    ->maxLength(400)
                    ->columnSpanFull(),

                Section::make('Hero image')
                    ->description('Shown at the top of the article and used to create the social sharing (OG) image and the blog card preview. When left empty, an OG image is generated automatically.')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('blog/heroes')
                            ->maxSize(10240)
                            ->rule(
                                Rule::dimensions()
                                    ->minWidth(ArticleImageService::OG_WIDTH)
                                    ->minHeight(ArticleImageService::OG_HEIGHT)
                            )
                            ->live()
                            ->helperText('At least '.ArticleImageService::OG_WIDTH.'×'.ArticleImageService::OG_HEIGHT.'px — upload the largest version you have; it will be scaled down if needed.'),

                        ImageCropper::make('og_image_crop')
                            ->label('Social (OG) image crop')
                            ->targetDimensions(ArticleImageService::OG_WIDTH, ArticleImageService::OG_HEIGHT)
                            ->imageUrl(fn (?Article $article) => $article?->getHeroImageUrl())
                            ->hasPendingImage(fn (?Article $article, Get $get): bool => static::heroImageIsPending($article, $get))
                            ->visible(fn (?Article $article) => filled($article?->hero_image)),

                        ImageCropper::make('card_image_crop')
                            ->label('Blog card crop')
                            ->targetDimensions(ArticleImageService::CARD_WIDTH, ArticleImageService::CARD_HEIGHT)
                            ->imageUrl(fn (?Article $article) => $article?->getHeroImageUrl())
                            ->hasPendingImage(fn (?Article $article, Get $get): bool => static::heroImageIsPending($article, $get))
                            ->visible(fn (?Article $article) => filled($article?->hero_image)),

                        ImageCropper::make('header_image_crop')
                            ->label('Article header crop')
                            ->targetDimensions(ArticleImageService::HEADER_WIDTH, ArticleImageService::HEADER_HEIGHT)
                            ->imageUrl(fn (?Article $article) => $article?->getHeroImageUrl())
                            ->hasPendingImage(fn (?Article $article, Get $get): bool => static::heroImageIsPending($article, $get))
                            ->visible(fn (?Article $article) => filled($article?->hero_image)),
                    ]),

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

    /**
     * Whether the hero image in the form state differs from the saved one,
     * meaning crops can only be adjusted after saving.
     */
    protected static function heroImageIsPending(?Article $article, Get $get): bool
    {
        return ! in_array($article?->hero_image, Arr::wrap($get('hero_image')), true);
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
