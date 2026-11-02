<?php

namespace App\Filament\Resources\WallOfLoveSubmissionResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\WallOfLoveSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWallOfLoveSubmission extends EditRecord
{
    protected static string $resource = WallOfLoveSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('user')
                ->icon('heroicon-s-user')
                ->visible(fn ($record): bool => $record->user !== null && UserResource::can('update', $record->user))
                ->url(fn ($record): string => UserResource::getUrl('edit', ['record' => $record->user_id])),
        ];
    }
}
