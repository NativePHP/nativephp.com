<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('email_verified_at'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Billing Information')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_id')
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('pm_type')
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('pm_last_four')
                            ->maxLength(4)
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->disabled(),
                        Forms\Components\TextInput::make('anystack_contact_id')
                            ->maxLength(255)
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->hidden()
                    ->searchable(),
                Tables\Columns\TextColumn::make('anystack_contact_id')
                    ->hidden()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('view_on_stripe')
                        ->label('View on Stripe')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (User $record) => 'https://dashboard.stripe.com/customers/'.$record->stripe_id)
                        ->openUrlInNewTab()
                        ->visible(fn (User $record) => filled($record->stripe_id)),
                    Tables\Actions\Action::make('view_on_anystack')
                        ->label('View on Anystack')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (User $record) => 'https://app.anystack.sh/contacts/'.$record->anystack_contact_id)
                        ->openUrlInNewTab()
                        ->visible(fn (User $record) => filled($record->anystack_contact_id)),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LicensesRelationManager::class,
            RelationManagers\SubscriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
