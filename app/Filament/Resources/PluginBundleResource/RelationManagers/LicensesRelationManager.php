<?php

namespace App\Filament\Resources\PluginBundleResource\RelationManagers;

use App\Models\PluginLicense;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'Bundle Purchases';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $bundleId = $this->ownerRecord->getKey();

                $query
                    ->select('plugin_licenses.*')
                    ->addSelect([
                        'sale_total' => PluginLicense::query()
                            ->from('plugin_licenses as sale_pl')
                            ->selectRaw('SUM(sale_pl.price_paid)')
                            ->whereColumn('sale_pl.user_id', 'plugin_licenses.user_id')
                            ->whereColumn('sale_pl.purchased_at', 'plugin_licenses.purchased_at')
                            ->where('sale_pl.plugin_bundle_id', $bundleId),
                        'sale_plugins_count' => PluginLicense::query()
                            ->from('plugin_licenses as count_pl')
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('count_pl.user_id', 'plugin_licenses.user_id')
                            ->whereColumn('count_pl.purchased_at', 'plugin_licenses.purchased_at')
                            ->where('count_pl.plugin_bundle_id', $bundleId),
                    ])
                    ->whereIn('plugin_licenses.id', function ($sub) use ($bundleId): void {
                        $sub->selectRaw('MIN(id)')
                            ->from('plugin_licenses as pl')
                            ->where('pl.plugin_bundle_id', $bundleId)
                            ->groupBy('pl.user_id', 'pl.purchased_at');
                    });
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sale_plugins_count')
                    ->label('Plugins')
                    ->badge(),

                Tables\Columns\TextColumn::make('sale_total')
                    ->label('Total')
                    ->formatStateUsing(fn (?int $state, PluginLicense $record): string => $record->is_grandfathered
                        ? '$0.00'
                        : '$'.number_format(($state ?? 0) / 100, 2)),

                Tables\Columns\IconColumn::make('is_grandfathered')
                    ->label('Granted')
                    ->boolean(),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('purchased_at', 'desc');
    }
}
