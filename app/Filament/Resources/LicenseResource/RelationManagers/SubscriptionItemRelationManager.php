<?php

namespace App\Filament\Resources\LicenseResource\RelationManagers;

use App\Filament\Resources\SubscriptionItemResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubscriptionItemRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptionItem';

    protected static bool $hasAssociateAction = false;

    protected static bool $hasDissociateAction = false;

    protected static bool $hasCreateAction = false;

    protected static bool $hasDeleteAction = false;

    protected static bool $hasEditAction = false;

    public function form(Schema $schema): Schema
    {
        return SubscriptionItemResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SubscriptionItemResource::table($table);
    }
}
