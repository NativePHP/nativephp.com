<?php

namespace App\Filament\Resources\SupportTicketResource\Pages;

use App\Filament\Resources\SupportTicketResource;
use App\Filament\Resources\SupportTicketResource\Widgets\TicketRepliesWidget;
use App\SupportTicket\Status;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('updateStatus')
                ->label('Update Status')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(collect(Status::cases())->mapWithKeys(fn (Status $s) => [$s->value => ucwords(str_replace('_', ' ', $s->value))]))
                        ->required(),
                ])
                ->fillForm(fn () => ['status' => $this->record->status->value])
                ->action(function (array $data): void {
                    $this->record->update(['status' => $data['status']]);
                    $this->refreshFormData(['status']);
                }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TicketRepliesWidget::class,
        ];
    }
}
