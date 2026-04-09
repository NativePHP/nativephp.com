<?php

namespace App\Filament\Resources\PluginResource\RelationManagers;

use App\Actions\RefundPluginPurchase;
use App\Models\PluginLicense;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'Purchase History';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Price Paid')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),

                Tables\Columns\IconColumn::make('is_grandfathered')
                    ->label('Comped')
                    ->boolean(),

                Tables\Columns\TextColumn::make('pluginBundle.name')
                    ->label('Bundle')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('refunded_at')
                    ->label('Refunded')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->actions([
                Actions\Action::make('refund')
                    ->label('Refund')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refund purchase')
                    ->modalDescription(function (PluginLicense $record): string {
                        $amount = '$'.number_format($record->price_paid / 100, 2);
                        $description = "This will issue a full {$amount} refund to {$record->user->email} and revoke their license.";

                        if ($record->wasPurchasedAsBundle()) {
                            $description .= ' This license was purchased as part of a bundle — all licenses in the bundle will be refunded.';
                        }

                        return $description;
                    })
                    ->modalSubmitActionLabel('Yes, refund')
                    ->action(function (PluginLicense $record): void {
                        try {
                            app(RefundPluginPurchase::class)->handle($record, auth()->user());

                            Notification::make()
                                ->title('Purchase refunded successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Refund failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (PluginLicense $record): bool => $record->isRefundable()),
            ]);
    }
}
