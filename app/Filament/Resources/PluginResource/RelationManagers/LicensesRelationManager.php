<?php

namespace App\Filament\Resources\PluginResource\RelationManagers;

use App\Filament\Actions\RefundPluginLicenseAction;
use App\Models\PluginLicense;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'Purchase History';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('refundedBy'))
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
                    ->description(fn (PluginLicense $record): ?string => $record->refundedBy ? 'by '.$record->refundedBy->email : null)
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('stripe_refund_id')
                    ->label('Stripe Refund')
                    ->fontFamily('mono')
                    ->copyable()
                    ->url(fn (PluginLicense $record): ?string => $record->stripePaymentUrl())
                    ->openUrlInNewTab()
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('refunded_at')
                    ->label('Refunded')
                    ->nullable(),
            ])
            ->actions([
                RefundPluginLicenseAction::make(),
            ]);
    }
}
