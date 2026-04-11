<?php

namespace App\Filament\Resources\SubscriptionItemResource\RelationManagers;

use App\Filament\Resources\LicenseResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    public function form(Schema $schema): Schema
    {
        return LicenseResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return LicenseResource::table($table);
    }
}
