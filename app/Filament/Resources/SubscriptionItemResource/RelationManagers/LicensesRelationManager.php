<?php

namespace App\Filament\Resources\SubscriptionItemResource\RelationManagers;

use App\Filament\Resources\LicenseResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    public function form(Form $form): Form
    {
        return LicenseResource::form($form);
    }

    public function table(Table $table): Table
    {
        return LicenseResource::table($table);
    }
}
