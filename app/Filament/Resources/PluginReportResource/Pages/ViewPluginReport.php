<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginReportResource\Pages;

use App\Filament\Resources\PluginReportResource;
use App\Models\PluginReport;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

final class ViewPluginReport extends ViewRecord
{
    protected static string $resource = PluginReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resolve')
                ->label('Mark Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record instanceof PluginReport && $this->record->isOpen())
                ->form([
                    Forms\Components\Textarea::make('resolution_note')
                        ->label('Resolution Note')
                        ->helperText('Optional internal note — not visible to the reporter or the plugin author.')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    /** @var PluginReport $report */
                    $report = $this->record;
                    $report->resolve(Auth::user(), $data['resolution_note'] ?: null);

                    Notification::make()
                        ->title('Report marked resolved')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('dismiss')
                ->label('Dismiss')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->visible(fn (): bool => $this->record instanceof PluginReport && $this->record->isOpen())
                ->requiresConfirmation()
                ->modalDescription('Dismiss this report as not actionable? It will be removed from the open reports count.')
                ->form([
                    Forms\Components\Textarea::make('resolution_note')
                        ->label('Resolution Note')
                        ->helperText('Optional internal note — not visible to the reporter or the plugin author.')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    /** @var PluginReport $report */
                    $report = $this->record;
                    $report->dismiss(Auth::user(), $data['resolution_note'] ?: null);

                    Notification::make()
                        ->title('Report dismissed')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
