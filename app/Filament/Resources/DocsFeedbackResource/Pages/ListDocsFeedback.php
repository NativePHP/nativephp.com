<?php

declare(strict_types=1);

namespace App\Filament\Resources\DocsFeedbackResource\Pages;

use App\Filament\Resources\DocsFeedbackResource;
use Filament\Resources\Pages\ListRecords;

final class ListDocsFeedback extends ListRecords
{
    protected static string $resource = DocsFeedbackResource::class;
}
