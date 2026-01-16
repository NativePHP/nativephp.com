<?php

namespace App\Filament\Resources\PluginBundleResource\Pages;

use App\Filament\Resources\PluginBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPluginBundle extends ViewRecord
{
    protected static string $resource = PluginBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewListing')
                ->label('View Listing Page')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('bundles.show', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->is_active && $this->record->published_at?->isPast()),
            Actions\EditAction::make(),
        ];
    }
}
