<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\PluginReportCategory;
use App\Enums\PluginReportStatus;
use App\Filament\Resources\PluginReportResource\Pages;
use App\Models\PluginReport;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class PluginReportResource extends Resource
{
    protected static ?string $model = PluginReport::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Plugin Reports';

    protected static \UnitEnum|string|null $navigationGroup = 'Products';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Plugin Report';

    protected static ?string $pluralModelLabel = 'Plugin Reports';

    protected static ?string $slug = 'plugin-reports';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PluginReport::query()->open()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    private static function resolutionActions(): array
    {
        return [
            Actions\Action::make('resolve')
                ->label('Mark Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (PluginReport $record): bool => $record->isOpen())
                ->form([
                    Forms\Components\Textarea::make('resolution_note')
                        ->label('Resolution Note')
                        ->helperText('Optional internal note — not visible to the reporter or the plugin author.')
                        ->rows(3),
                ])
                ->action(function (PluginReport $record, array $data): void {
                    $record->resolve(Auth::user(), $data['resolution_note'] ?: null);

                    Notification::make()
                        ->title('Report marked resolved')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('dismiss')
                ->label('Dismiss')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->visible(fn (PluginReport $record): bool => $record->isOpen())
                ->requiresConfirmation()
                ->modalDescription('Dismiss this report as not actionable? It will be removed from the open reports count.')
                ->form([
                    Forms\Components\Textarea::make('resolution_note')
                        ->label('Resolution Note')
                        ->helperText('Optional internal note — not visible to the reporter or the plugin author.')
                        ->rows(3),
                ])
                ->action(function (PluginReport $record, array $data): void {
                    $record->dismiss(Auth::user(), $data['resolution_note'] ?: null);

                    Notification::make()
                        ->title('Report dismissed')
                        ->success()
                        ->send();
                }),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Schemas\Components\Section::make('Report Details')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('plugin.name')
                            ->label('Plugin'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Reported By')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('category')
                            ->badge()
                            ->formatStateUsing(fn (PluginReportCategory $state): string => $state->label()),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (PluginReportStatus $state): string => $state->color())
                            ->formatStateUsing(fn (PluginReportStatus $state): string => $state->label()),
                        Infolists\Components\TextEntry::make('message')
                            ->label('Report Message')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Filed At')
                            ->dateTime(),
                    ]),

                Schemas\Components\Section::make('Resolution')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('resolvedBy.email')
                            ->label('Resolved By')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('resolved_at')
                            ->label('Resolved At')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('resolution_note')
                            ->label('Note')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (PluginReport $record): bool => ! $record->isOpen()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plugin.name')
                    ->label('Plugin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Reported By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (PluginReportCategory $state): string => $state->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (PluginReportStatus $state): string => $state->color())
                    ->formatStateUsing(fn (PluginReportStatus $state): string => $state->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn (PluginReport $record): string => $record->message),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Filed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PluginReportStatus::class),
                Tables\Filters\SelectFilter::make('category')
                    ->options(PluginReportCategory::class),
            ])
            ->actions([
                Actions\ViewAction::make(),
                ...self::resolutionActions(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn ($record) => self::getUrl('view', ['record' => $record])
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPluginReports::route('/'),
            'view' => Pages\ViewPluginReport::route('/{record}'),
        ];
    }
}
