<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('License Information')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->disabled(),
                        Forms\Components\TextInput::make('anystack_id')
                            ->maxLength(36)
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'email')
                            ->disabled(),
                        Forms\Components\TextInput::make('policy_name')
                            ->label('Plan')
                            ->disabled(),
                        Forms\Components\TextInput::make('key')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
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
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->url(fn (\App\Models\License $record): string => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('policy_name')
                    ->label('Plan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->modalHeading('License Details')
                    ->modalWidth('7xl')
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('viewUser')
                            ->label('View User')
                            ->icon('heroicon-o-user')
                            ->color('primary')
                            ->url(fn (License $record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                            ->openUrlInNewTab()
                            ->visible(fn (License $record) => $record->user_id !== null),
                    ]),
            ])
            ->defaultPaginationPageOption(25)
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('License Information')
                    ->schema([
                        Components\TextEntry::make('id'),
                        Components\TextEntry::make('key')
                            ->copyable(),
                        Components\TextEntry::make('policy_name')
                            ->label('Plan'),
                        Components\TextEntry::make('expires_at')
                            ->dateTime(),
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Components\TextEntry::make('anystack_id')
                            ->copyable(),
                    ])->columns(2),

                Components\Section::make('User Information')
                    ->schema([
                        Components\TextEntry::make('user.id')
                            ->label('User ID')
                            ->url(fn ($record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                            ->openUrlInNewTab(),
                        Components\TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable()
                            ->url(fn ($record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                            ->openUrlInNewTab(),
                        Components\TextEntry::make('user.name')
                            ->label('Name'),
                        Components\TextEntry::make('user.first_name')
                            ->label('First Name'),
                        Components\TextEntry::make('user.last_name')
                            ->label('Last Name'),
                        Components\TextEntry::make('user.stripe_id')
                            ->label('Stripe ID')
                            ->copyable()
                            ->visible(fn ($record) => filled($record->user->stripe_id))
                            ->url(fn ($record) => filled($record->user->stripe_id)
                                ? "https://dashboard.stripe.com/customers/{$record->user->stripe_id}"
                                : null)
                            ->openUrlInNewTab(),
                        Components\TextEntry::make('user.anystack_contact_id')
                            ->label('Anystack Contact ID')
                            ->copyable()
                            ->visible(fn ($record) => filled($record->user->anystack_contact_id))
                            ->url(fn ($record) => filled($record->user->anystack_contact_id)
                                ? "https://app.anystack.sh/contacts/{$record->user->anystack_contact_id}"
                                : null)
                            ->openUrlInNewTab(),
                    ])->columns(2),

                Components\Section::make('Subscription Information')
                    ->schema([
                        Components\TextEntry::make('subscriptionItem.id')
                            ->label('Subscription Item ID')
                            ->visible(fn ($record) => $record->subscription_item_id !== null),
                        Components\TextEntry::make('subscriptionItem.stripe_id')
                            ->label('Stripe Subscription Item ID')
                            ->copyable()
                            ->visible(fn ($record) => $record->subscription_item_id !== null),
                        Components\TextEntry::make('subscriptionItem.subscription.stripe_id')
                            ->label('Stripe Subscription ID')
                            ->copyable()
                            ->visible(fn ($record) => $record->subscription_item_id !== null)
                            ->url(fn ($record) => $record->subscription_item_id !== null && filled($record->subscriptionItem?->subscription?->stripe_id)
                                ? "https://dashboard.stripe.com/subscriptions/{$record->subscriptionItem->subscription->stripe_id}"
                                : null)
                            ->openUrlInNewTab(),
                        Components\TextEntry::make('subscriptionItem.stripe_price')
                            ->label('Stripe Price ID')
                            ->copyable()
                            ->visible(fn ($record) => $record->subscription_item_id !== null),
                        Components\TextEntry::make('subscriptionItem.stripe_product')
                            ->label('Stripe Product ID')
                            ->copyable()
                            ->visible(fn ($record) => $record->subscription_item_id !== null),
                        Components\TextEntry::make('subscriptionItem.subscription.stripe_status')
                            ->label('Subscription Status')
                            ->badge()
                            ->color(fn ($state): string => match ($state) {
                                'active' => 'success',
                                'canceled' => 'danger',
                                'incomplete' => 'warning',
                                'incomplete_expired' => 'danger',
                                'past_due' => 'warning',
                                'trialing' => 'info',
                                'unpaid' => 'danger',
                                default => 'gray',
                            })
                            ->visible(fn ($record) => $record->subscription_item_id !== null),
                    ])->columns(2)
                    ->visible(fn ($record) => $record->subscription_item_id !== null),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
        ];
    }
}
