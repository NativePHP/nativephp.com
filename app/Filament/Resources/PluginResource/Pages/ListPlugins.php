<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Enums\PluginStatus;
use App\Filament\Resources\PluginResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPlugins extends ListRecords
{
    protected static string $resource = PluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            PluginStatus::Pending->value => $this->makeStatusTab(PluginStatus::Pending, 'Pending'),
            PluginStatus::Approved->value => $this->makeStatusTab(PluginStatus::Approved, 'Approved'),
            PluginStatus::Rejected->value => $this->makeStatusTab(PluginStatus::Rejected, 'Rejected'),
            'all' => Tab::make('All'),
        ];
    }

    /**
     * The status the active tab is scoped to, or null when the tab lists every status.
     */
    public function getActiveTabStatus(): ?PluginStatus
    {
        return PluginStatus::tryFrom($this->activeTab ?? '');
    }

    private function makeStatusTab(PluginStatus $status, string $label): Tab
    {
        return Tab::make($label)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status));
    }
}
