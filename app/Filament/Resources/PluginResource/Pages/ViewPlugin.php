<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Filament\Resources\PluginResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewPlugin extends ViewRecord
{
    protected static string $resource = PluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewListing')
                ->label('View Listing Page')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('plugins.show', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->isApproved()),

            Actions\Action::make('approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn () => $this->record->isPending())
                ->action(fn () => $this->record->approve(auth()->id()))
                ->requiresConfirmation()
                ->modalHeading('Approve Plugin')
                ->modalDescription(fn () => "Are you sure you want to approve '{$this->record->name}'?"),

            Actions\Action::make('reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn () => $this->record->isPending() || $this->record->isApproved())
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required()
                        ->rows(3)
                        ->placeholder('Please explain why this plugin is being rejected...'),
                ])
                ->action(fn (array $data) => $this->record->reject($data['rejection_reason'], auth()->id()))
                ->modalHeading('Reject Plugin')
                ->modalDescription(fn () => "Are you sure you want to reject '{$this->record->name}'?"),
        ];
    }
}
