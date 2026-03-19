<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Filament\Resources\SubscriptionItemResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SubscriptionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return SubscriptionItemResource::form($form);
    }

    public function table(Table $table): Table
    {
        return SubscriptionItemResource::table($table);
    }
}
