<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpenCollectiveDonationResource\Pages;
use App\Models\OpenCollectiveDonation;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Number;

class OpenCollectiveDonationResource extends Resource
{
    protected static ?string $model = OpenCollectiveDonation::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'OpenCollective Donations';

    protected static ?string $modelLabel = 'Donation';

    protected static ?string $pluralModelLabel = 'Donations';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Donation Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_id')
                            ->label('Order ID')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('order_idv2')
                            ->label('Order ID (v2)')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('amount')
                            ->formatStateUsing(fn ($state, $record) => Number::currency($state / 100, $record->currency)),
                        Infolists\Components\TextEntry::make('interval')
                            ->default('One-time'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime(),
                    ])->columns(2),
                Infolists\Components\Section::make('Contributor')
                    ->schema([
                        Infolists\Components\TextEntry::make('from_collective_name')
                            ->label('Name'),
                        Infolists\Components\TextEntry::make('from_collective_slug')
                            ->label('Slug')
                            ->url(fn ($state) => "https://opencollective.com/{$state}")
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('from_collective_id')
                            ->label('Collective ID'),
                    ])->columns(3),
                Infolists\Components\Section::make('Claim Status')
                    ->schema([
                        Infolists\Components\IconEntry::make('claimed_at')
                            ->label('Claimed')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        Infolists\Components\TextEntry::make('claimed_at')
                            ->label('Claimed At')
                            ->dateTime()
                            ->placeholder('Not claimed'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Claimed By')
                            ->placeholder('Not claimed')
                            ->url(fn ($record) => $record->user_id ? UserResource::getUrl('edit', ['record' => $record->user_id]) : null),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('from_collective_name')
                    ->label('Contributor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state, $record) => Number::currency($state / 100, $record->currency))
                    ->sortable(),
                Tables\Columns\TextColumn::make('interval')
                    ->badge()
                    ->default('One-time')
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray'),
                Tables\Columns\IconColumn::make('claimed_at')
                    ->label('Claimed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Claimed By')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('claimed')
                    ->label('Claimed')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('claimed_at')),
                Tables\Filters\Filter::make('unclaimed')
                    ->label('Unclaimed')
                    ->query(fn (Builder $query): Builder => $query->whereNull('claimed_at')),
                Tables\Filters\Filter::make('recurring')
                    ->label('Recurring')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('interval')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpenCollectiveDonations::route('/'),
            'view' => Pages\ViewOpenCollectiveDonation::route('/{record}'),
        ];
    }
}
