<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\SubscriptionResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public function form(Form $form): Form
    {
        return SubscriptionResource::form($form);
    }

    public function table(Table $table): Table
    {
        return SubscriptionResource::table($table);
    }
}
