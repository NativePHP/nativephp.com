<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Actions\RefundPluginPurchase;
use App\Enums\PluginType;
use App\Models\PluginLicense;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_grandfathered')
                    ->label('Comped'),
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
                Actions\Action::make('refund')
                    ->label('Refund')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refund purchase')
                    ->modalDescription(function (PluginLicense $record): string {
                        $amount = '$'.number_format($record->price_paid / 100, 2);
                        $description = "This will issue a full {$amount} refund to {$record->user->email} for {$record->plugin->name} and revoke their license.";

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
                Actions\DeleteAction::make(),
            ]);
    }
}
