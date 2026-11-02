<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PluginIdeaResource\Pages;
use App\Filament\Resources\PluginIdeaResource\RelationManagers;
use App\Models\PluginIdea;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

final class PluginIdeaResource extends Resource
{
    protected static ?string $model = PluginIdea::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Plugin Ideas';

    protected static \UnitEnum|string|null $navigationGroup = 'Products';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Plugin Idea';

    protected static ?string $pluralModelLabel = 'Plugin Ideas';

    protected static ?string $slug = 'plugin-ideas';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Jev matches new searches against the title and description, so keep them clear.'),
                Forms\Components\Textarea::make('description')
                    ->rows(3),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Schemas\Components\Section::make('Idea')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('title'),
                        Infolists\Components\TextEntry::make('description')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('votes')
                            ->state(fn (PluginIdea $record): int => $record->missedSearches()->count()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('First Searched')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('last_searched_at')
                            ->label('Last Searched')
                            ->state(fn (PluginIdea $record): ?string => $record->missedSearches()->max('created_at'))
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->description(fn (PluginIdea $record): ?string => $record->description ? Str::limit($record->description, 150) : null),

                Tables\Columns\TextColumn::make('missed_searches_count')
                    ->label('Votes')
                    ->counts('missedSearches')
                    ->sortable(),

                Tables\Columns\TextColumn::make('missed_searches_max_created_at')
                    ->label('Last Searched')
                    ->max('missedSearches', 'created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('First Searched')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('missed_searches_count', 'desc')
            ->recordUrl(
                fn (PluginIdea $record): string => self::getUrl('view', ['record' => $record])
            );
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MissedSearchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPluginIdeas::route('/'),
            'view' => Pages\ViewPluginIdea::route('/{record}'),
            'edit' => Pages\EditPluginIdea::route('/{record}/edit'),
        ];
    }
}
