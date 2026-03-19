<?php

namespace App\Filament\Resources\LicenseResource\RelationManagers;

use App\Filament\Resources\SubscriptionItemResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SubscriptionItemRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptionItem';

    protected static bool $hasAssociateAction = false;

    protected static bool $hasDissociateAction = false;

    protected static bool $hasCreateAction = false;

    protected static bool $hasDeleteAction = false;

    protected static bool $hasEditAction = false;

    public function form(Form $form): Form
    {
        return SubscriptionItemResource::form($form);
    }

    public function table(Table $table): Table
    {
        return SubscriptionItemResource::table($table);
    }
}
