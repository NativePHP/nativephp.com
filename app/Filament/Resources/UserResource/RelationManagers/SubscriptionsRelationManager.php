<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public function form(Schema $schema): Schema
    {
        return SubscriptionResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SubscriptionResource::table($table);
    }
}
