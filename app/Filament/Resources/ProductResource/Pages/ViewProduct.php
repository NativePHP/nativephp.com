<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewListing')
                ->label('View Listing Page')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('products.show', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->is_active && $this->record->published_at?->isPast()),
            Actions\EditAction::make(),
        ];
    }
}
