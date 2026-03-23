<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Filament\Resources\SubscriptionItemResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubscriptionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return SubscriptionItemResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SubscriptionItemResource::table($table);
    }
}
