<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Schemas\Components\Section::make('User Information')
                    ->inlineLabel()
                    ->columns(1)
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
                    ]),
                Schemas\Components\Section::make('Billing Information')
                    ->inlineLabel()
                    ->columns(1)
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
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
                Impersonate::make(),
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\Action::make('view_on_stripe')
                        ->label('View on Stripe')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (User $record) => 'https://dashboard.stripe.com/customers/'.$record->stripe_id)
                        ->openUrlInNewTab()
                        ->visible(fn (User $record) => filled($record->stripe_id)),
                    Actions\Action::make('view_on_anystack')
                        ->label('View on Anystack')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (User $record) => 'https://app.anystack.sh/contacts/'.$record->anystack_contact_id)
                        ->openUrlInNewTab()
                        ->visible(fn (User $record) => filled($record->anystack_contact_id)),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(
                fn ($record) => static::getUrl('edit', ['record' => $record])
            );
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PluginLicensesRelationManager::class,
            RelationManagers\ProductLicensesRelationManager::class,
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
