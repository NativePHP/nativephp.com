<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalAccessTokenResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?string $modelLabel = 'API Key';

    protected static ?string $pluralModelLabel = 'API Keys';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('A descriptive name for this API key'),

                Forms\Components\Select::make('tokenable_id')
                    ->label('User')
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\Hidden::make('tokenable_type')
                    ->default(\App\Models\User::class),

                Forms\Components\TextInput::make('abilities')
                    ->default('*')
                    ->helperText('Comma-separated list of abilities. Use * for all abilities.'),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->nullable()
                    ->helperText('Leave empty for no expiration'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tokenable.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('abilities')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalAccessTokens::route('/'),
            'create' => Pages\CreatePersonalAccessToken::route('/create'),
        ];
    }
}
