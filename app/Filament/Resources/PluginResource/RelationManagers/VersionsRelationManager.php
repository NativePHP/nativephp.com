<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginResource\RelationManagers;

use App\Models\PluginVersion;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class VersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    protected static ?string $title = 'Versions';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('version')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tag_name')
                    ->label('Tag')
                    ->searchable(),

                Tables\Columns\IconColumn::make('permissions_expanded')
                    ->label('Permissions Expanded')
                    ->boolean(),

                Tables\Columns\IconColumn::make('requires_review')
                    ->label('Pending Review')
                    ->boolean()
                    ->trueColor('danger'),

                Tables\Columns\TextColumn::make('approvedBy.email')
                    ->label('Approved By')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Actions\Action::make('viewManifest')
                    ->label('View Permissions')
                    ->icon('heroicon-o-shield-check')
                    ->color('gray')
                    ->visible(fn (PluginVersion $record): bool => filled($record->manifest_permissions))
                    ->modalHeading(fn (PluginVersion $record): string => "Permissions — {$record->version}")
                    ->modalContent(fn (PluginVersion $record) => view(
                        'filament.resources.plugin-resource.manifest-permissions',
                        ['manifest' => $record->manifest_permissions]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PluginVersion $record): bool => $record->requires_review)
                    ->requiresConfirmation()
                    ->modalDescription('This version will become the plugin\'s current visible version.')
                    ->action(function (PluginVersion $record): void {
                        $record->approve(Auth::user());

                        $latest = $record->plugin->versions()->visible()->latest('published_at')->first();

                        if ($latest) {
                            $record->plugin->update(['latest_version' => $latest->version]);
                        }

                        Notification::make()
                            ->title("Version {$record->version} approved")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('published_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
