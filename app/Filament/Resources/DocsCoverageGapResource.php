<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DocsCoverageCategory;
use App\Enums\DocsPlatform;
use App\Filament\Resources\DocsCoverageGapResource\Pages;
use App\Models\DocsCoverageGap;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class DocsCoverageGapResource extends Resource
{
    protected static ?string $model = DocsCoverageGap::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Docs Coverage';

    protected static ?string $pluralModelLabel = 'Docs Coverage Gaps';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')
                    ->formatStateUsing(fn (DocsPlatform $state): string => $state->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->formatStateUsing(fn (DocsCoverageCategory $state): string => $state->label())
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('identifier')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('documented')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source_path')
                    ->label('Source')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Last checked')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->options(collect(DocsPlatform::cases())->mapWithKeys(
                        fn (DocsPlatform $platform) => [$platform->value => $platform->label()]
                    )),
                Tables\Filters\SelectFilter::make('category')
                    ->options(collect(DocsCoverageCategory::cases())->mapWithKeys(
                        fn (DocsCoverageCategory $category) => [$category->value => $category->label()]
                    )),
                Tables\Filters\TernaryFilter::make('documented'),
            ])
            ->actions([
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('documented');
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
            'index' => Pages\ListDocsCoverageGaps::route('/'),
        ];
    }
}
