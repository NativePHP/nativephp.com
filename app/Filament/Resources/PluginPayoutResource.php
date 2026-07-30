<?php

namespace App\Filament\Resources;

use App\Enums\PayoutStatus;
use App\Filament\Resources\PluginPayoutResource\Pages;
use App\Filament\Resources\PluginPayoutResource\RelationManagers;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\PluginPayout;
use Filament\Actions;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PluginPayoutResource extends Resource
{
    protected static ?string $model = PluginPayout::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Payouts';

    protected static \UnitEnum|string|null $navigationGroup = 'Products';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Payout';

    protected static ?string $pluralModelLabel = 'Payouts';

    protected static ?string $slug = 'plugin-payouts';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Schemas\Components\Section::make('Payout Details')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('pluginLicense.plugin.display_name')
                            ->label('Plugin')
                            ->default(fn (PluginPayout $record): ?string => $record->pluginLicense?->plugin?->name),
                        Infolists\Components\TextEntry::make('pluginLicense.user.email')
                            ->label('Customer')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('developerAccount.user.email')
                            ->label('Seller')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (PayoutStatus $state): string => $state->color())
                            ->formatStateUsing(fn (PayoutStatus $state): string => $state->label()),
                        Infolists\Components\TextEntry::make('gross_amount')
                            ->label('Customer Paid')
                            ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),
                        Infolists\Components\TextEntry::make('developer_amount')
                            ->label('Due to Seller')
                            ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),
                        Infolists\Components\TextEntry::make('platform_fee')
                            ->label('Platform Fee')
                            ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),
                        Infolists\Components\TextEntry::make('stripe_transfer_id')
                            ->label('Stripe Transfer ID')
                            ->copyable()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('attempt_count')
                            ->label('Total Attempts'),
                        Infolists\Components\TextEntry::make('last_attempted_at')
                            ->label('Last Attempt')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('transferred_at')
                            ->label('Transferred At')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('eligible_for_payout_at')
                            ->label('Eligible From')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                    ]),

                Schemas\Components\Section::make('Latest Failure Reason')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('failure_reason')
                            ->label('Reason')
                            ->placeholder('—'),
                    ])
                    ->visible(fn (PluginPayout $record): bool => $record->failure_reason !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pluginLicense.plugin.name')
                    ->label('Plugin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pluginLicense.user.email')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Paid')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('developer_amount')
                    ->label('Due to Seller')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (PayoutStatus $state): string => $state->color())
                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->label())
                    ->sortable(),

                Tables\Columns\IconColumn::make('paid_out')
                    ->label('Paid Out')
                    ->boolean()
                    ->getStateUsing(fn (PluginPayout $record): bool => $record->isTransferred()),

                Tables\Columns\TextColumn::make('attempt_count')
                    ->label('Attempts')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(PayoutStatus::cases())
                        ->mapWithKeys(fn (PayoutStatus $status) => [$status->value => $status->label()])
                        ->toArray()),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\Action::make('retryPayout')
                    ->label('Retry Payout')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Retry Payout')
                    ->modalDescription('This will reset the payout to pending and dispatch the transfer job. Continue?')
                    ->modalSubmitActionLabel('Retry')
                    ->visible(fn (PluginPayout $record): bool => $record->isFailed())
                    ->action(function (PluginPayout $record): void {
                        $record->update([
                            'status' => PayoutStatus::Pending,
                        ]);

                        ProcessPayoutTransfer::dispatch($record);

                        Notification::make()
                            ->title("Payout #{$record->id} queued for retry")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn ($record) => static::getUrl('view', ['record' => $record])
            );
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttemptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPluginPayouts::route('/'),
            'view' => Pages\ViewPluginPayout::route('/{record}'),
        ];
    }
}
