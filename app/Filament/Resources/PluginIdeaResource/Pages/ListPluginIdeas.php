<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginIdeaResource\Pages;

use App\Filament\Resources\PluginIdeaResource;
use Filament\Resources\Pages\ListRecords;

final class ListPluginIdeas extends ListRecords
{
    protected static string $resource = PluginIdeaResource::class;

    protected ?string $subheading = 'Plugins people searched for through the MCP server and didn\'t find. Jev groups searches that ask for the same thing, and each one counts as a vote.';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
