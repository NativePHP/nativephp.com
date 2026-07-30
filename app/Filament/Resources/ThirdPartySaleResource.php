<?php

namespace App\Filament\Resources;

use App\Enums\PayoutStatus;
use App\Enums\PluginType;
use App\Filament\Resources\ThirdPartySaleResource\Pages;
use App\Models\PluginLicense;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ThirdPartySaleResource extends Resource
{
    protected static ?string $model = PluginLicense::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Third-Party Sales';

    protected static \UnitEnum|string|null $navigationGroup = 'Products';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Third-Party Sale';

    protected static ?string $pluralModelLabel = 'Third-Party Sales';

    protected static ?string $slug = 'third-party-sales';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('plugin', function (Builder $query): void {
                $query->where('is_official', false)
                    ->where('type', PluginType::Paid);
            })
            ->where('is_grandfathered', false)
            ->with(['plugin.developerAccount.user', 'user', 'payout']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plugin.name')
                    ->label('Plugin')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plugin.developerAccount.user.email')
                    ->label('Seller')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Paid')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('payout.developer_amount')
                    ->label('Due to Seller')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : '$'.number_format($state / 100, 2))
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('payout_status')
                    ->label('Payout')
                    ->badge()
                    ->getStateUsing(function (PluginLicense $record): string {
                        if ($record->payout) {
                            return $record->payout->status->label();
                        }

                        return $record->price_paid > 0 ? 'Missing' : 'N/A';
                    })
                    ->color(function (PluginLicense $record): string {
                        if ($record->payout) {
                            return $record->payout->status->color();
                        }

                        return $record->price_paid > 0 ? 'danger' : 'gray';
                    }),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('missing_payout')
                    ->label('Missing Payout')
                    ->queries(
                        true: fn (Builder $query) => $query->whereDoesntHave('payout')->where('price_paid', '>', 0),
                        false: fn (Builder $query) => $query->whereHas('payout'),
                    ),

                Tables\Filters\SelectFilter::make('payout_status')
                    ->label('Payout Status')
                    ->options(collect(PayoutStatus::cases())
                        ->mapWithKeys(fn (PayoutStatus $status) => [$status->value => $status->label()])
                        ->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('payout', fn (Builder $q) => $q->where('status', $data['value']));
                    }),

                Tables\Filters\SelectFilter::make('plugin_id')
                    ->label('Plugin')
                    ->relationship('plugin', 'name', fn (Builder $query) => $query->where('is_official', false)->where('type', PluginType::Paid))
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Actions\Action::make('viewPayout')
                    ->label('View Payout')
                    ->icon('heroicon-o-currency-dollar')
                    ->url(fn (PluginLicense $record): ?string => $record->payout
                        ? PluginPayoutResource::getUrl('view', ['record' => $record->payout])
                        : null)
                    ->visible(fn (PluginLicense $record): bool => $record->payout !== null),
            ])
            ->bulkActions([])
            ->defaultSort('purchased_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThirdPartySales::route('/'),
        ];
    }
}
