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
use Illuminate\Support\HtmlString;
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
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->hidden(fn (string $context): bool => $context === 'edit')
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
                Schemas\Components\Section::make('Notifications')
                    ->description('Once these are disabled, they cannot be re-enabled by an admin.')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Forms\Components\Toggle::make('receives_notification_emails')
                            ->label('Email notifications')
                            ->disabled(fn (?User $record) => $record && ! $record->receives_notification_emails),
                        Forms\Components\Toggle::make('receives_new_plugin_notifications')
                            ->label('New plugin notifications')
                            ->disabled(fn (?User $record) => $record && ! $record->receives_new_plugin_notifications),
                    ]),
                Schemas\Components\Section::make('Developer Account')
                    ->inlineLabel()
                    ->columns(1)
                    ->visible(fn (?User $record) => $record?->developerAccount !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('developerAccount.stripe_connect_status')
                            ->label('Stripe Connect Status')
                            ->content(fn (User $record) => $record->developerAccount->stripe_connect_status?->label() ?? '—'),
                        Forms\Components\Placeholder::make('developerAccount.stripe_connect_account_id')
                            ->label('Stripe Connect Account')
                            ->content(fn (User $record) => new HtmlString(
                                '<a href="https://dashboard.stripe.com/connect/accounts/'
                                .e($record->developerAccount->stripe_connect_account_id)
                                .'" target="_blank" class="text-primary-600 hover:underline">'
                                .e($record->developerAccount->stripe_connect_account_id)
                                .' &#8599;</a>'
                            )),
                        Forms\Components\Placeholder::make('developerAccount.country')
                            ->label('Country')
                            ->content(fn (User $record) => $record->developerAccount->country ?? '—'),
                        Forms\Components\Placeholder::make('developerAccount.payout_currency')
                            ->label('Payout Currency')
                            ->content(fn (User $record) => strtoupper($record->developerAccount->payout_currency ?? '—')),
                        Forms\Components\Placeholder::make('developerAccount.payouts_enabled')
                            ->label('Payouts Enabled')
                            ->content(fn (User $record) => $record->developerAccount->payouts_enabled ? 'Yes' : 'No'),
                        Forms\Components\Placeholder::make('developerAccount.charges_enabled')
                            ->label('Charges Enabled')
                            ->content(fn (User $record) => $record->developerAccount->charges_enabled ? 'Yes' : 'No'),
                        Forms\Components\Placeholder::make('developerAccount.onboarding_completed_at')
                            ->label('Onboarding Completed')
                            ->content(fn (User $record) => $record->developerAccount->onboarding_completed_at?->format('M j, Y g:i A') ?? '—'),
                        Forms\Components\Placeholder::make('developerAccount.accepted_plugin_terms_at')
                            ->label('Plugin Terms Accepted')
                            ->content(fn (User $record) => $record->developerAccount->accepted_plugin_terms_at?->format('M j, Y g:i A') ?? '—'),
                        Forms\Components\TextInput::make('developerAccount.payout_percentage')
                            ->label('Payout Percentage')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                        Forms\Components\Placeholder::make('developerAccount.plugin_terms_version')
                            ->label('Terms Version')
                            ->content(fn (User $record) => $record->developerAccount->plugin_terms_version ?? '—'),
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
                Tables\Columns\IconColumn::make('developerAccount.id')
                    ->label('Developer')
                    ->boolean()
                    ->getStateUsing(fn (User $record) => $record->developerAccount !== null),
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
            RelationManagers\DeveloperPluginsRelationManager::class,
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
