<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DocsPlatform;
use App\Filament\Resources\DocsFeedbackResource\Pages;
use App\Models\DocsFeedback;
use Filament\Actions;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

final class DocsFeedbackResource extends Resource
{
    protected static ?string $model = DocsFeedback::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-hand-thumb-up';

    protected static ?string $navigationLabel = 'Docs Feedback';

    protected static ?string $pluralModelLabel = 'Docs Feedback';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Schemas\Components\Section::make('Feedback')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('page')
                            ->label('Page'),
                        Infolists\Components\TextEntry::make('platform'),
                        Infolists\Components\TextEntry::make('version'),
                        Infolists\Components\IconEntry::make('helpful')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('comment')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Submitted')
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('platform')
                    ->sortable(),

                Tables\Columns\TextColumn::make('version')
                    ->sortable(),

                Tables\Columns\IconColumn::make('helpful')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->limit(60)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->options(collect(DocsPlatform::cases())->mapWithKeys(
                        fn (DocsPlatform $platform) => [$platform->value => ucfirst($platform->value)]
                    )),
                Tables\Filters\TernaryFilter::make('helpful'),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDocsFeedback::route('/'),
            'view' => Pages\ViewDocsFeedback::route('/{record}'),
        ];
    }
}
