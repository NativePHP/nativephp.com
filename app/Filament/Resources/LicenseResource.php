<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Filament\Resources\LicenseResource\RelationManagers;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
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
                        Forms\Components\TextInput::make('id'),
                        Forms\Components\TextInput::make('anystack_id')
                            ->maxLength(36),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'email'),
                        Forms\Components\TextInput::make('policy_name')
                            ->label('Plan'),
                        Forms\Components\TextInput::make('key'),
                        Forms\Components\DateTimePicker::make('expires_at'),
                        Forms\Components\DateTimePicker::make('created_at'),
                        Forms\Components\Toggle::make('is_suspended')
                            ->label('Suspended'),
                    ])
                    ->columns(2)
                    ->disabled(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_item_id')
                    ->label('Subscription Item')
                    ->searchable()
                    ->sortable(),
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
                Tables\Columns\IconColumn::make('is_suspended')
                    ->boolean()
                    ->label('Suspended')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('viewUser')
                        ->label('View User')
                        ->icon('heroicon-o-user')
                        ->color('primary')
                        ->url(fn (License $record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                        ->openUrlInNewTab()
                        ->visible(fn (License $record) => $record->user_id !== null),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->defaultPaginationPageOption(25)
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('License Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('anystack_id')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('User')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('policy_name')
                            ->label('Plan')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('key')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('expires_at')
                            ->dateTime()
                            ->copyable(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->copyable(),
                        Infolists\Components\IconEntry::make('is_suspended')
                            ->label('Suspended')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserRelationManager::class,
            RelationManagers\SubscriptionItemRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'view' => Pages\ViewLicense::route('/{record}'),
        ];
    }
}
