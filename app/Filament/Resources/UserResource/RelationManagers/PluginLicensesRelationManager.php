<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\PluginType;
use App\Filament\Actions\RefundPluginLicenseAction;
use App\Models\PluginLicense;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PluginLicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'pluginLicenses';

    protected static ?string $title = 'Plugins';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('plugin_id')
                    ->relationship('plugin', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Toggle::make('is_grandfathered')
                    ->label('Comped')
                    ->default(true),
                Forms\Components\DateTimePicker::make('purchased_at')
                    ->default(now()),
                Forms\Components\DateTimePicker::make('expires_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plugin.name')
                    ->label('Plugin')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('plugin.type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (PluginType $state): string => match ($state) {
                        PluginType::Free => 'gray',
                        PluginType::Paid => 'success',
                    }),
                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Price Paid')
                    ->money('usd', divideBy: 100)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_grandfathered')
                    ->label('Comped')
                    ->boolean(),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with('refundedBy'))
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_grandfathered')
                    ->label('Comped'),
                Tables\Filters\TernaryFilter::make('refunded_at')
                    ->label('Refunded')
                    ->nullable(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['price_paid'] = 0;
                        $data['currency'] = 'USD';

                        return $data;
                    }),
            ])
            ->actions([
                RefundPluginLicenseAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }
}
