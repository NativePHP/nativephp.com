<?php

namespace App\Filament\Resources;

use App\Enums\PluginStatus;
use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Filament\Resources\PluginResource\Pages;
use App\Filament\Resources\PluginResource\RelationManagers;
use App\Models\Plugin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Plugins';

    protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Plugins';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plugin Details')
                    ->schema([
                        Forms\Components\Placeholder::make('logo_preview')
                            ->label('Logo')
                            ->content(fn (?Plugin $record) => $record?->hasLogo()
                                ? new \Illuminate\Support\HtmlString('<img src="'.e($record->getLogoUrl()).'" alt="Logo" class="w-16 h-16 rounded-lg object-cover" />')
                                : 'No logo')
                            ->visible(fn (?Plugin $record) => $record !== null),

                        Forms\Components\TextInput::make('name')
                            ->label('Composer Package Name'),

                        Forms\Components\Select::make('type')
                            ->options(PluginType::class),

                        Forms\Components\Select::make('tier')
                            ->options(PluginTier::class)
                            ->placeholder('No tier')
                            ->helperText('Set pricing tier for paid plugins'),

                        Forms\Components\TextInput::make('repository_url')
                            ->label('Repository URL')

                            ->url()
                            ->suffixIcon('heroicon-o-arrow-top-right-on-square')
                            ->suffixIconColor('gray'),

                        Forms\Components\Select::make('status')
                            ->options(PluginStatus::class),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')

                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')

                            ->visible(fn (?Plugin $record) => $record?->isRejected()),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Submission Info')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Submitted At'),

                        Forms\Components\Select::make('approved_by')
                            ->relationship('approvedBy', 'email')

                            ->visible(fn (?Plugin $record) => $record?->approved_by !== null),

                        Forms\Components\DateTimePicker::make('approved_at')

                            ->visible(fn (?Plugin $record) => $record?->approved_at !== null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=P&color=7C3AED&background=EDE9FE')
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Package Name')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (PluginType $state): string => match ($state) {
                        PluginType::Free => 'gray',
                        PluginType::Paid => 'success',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tier')
                    ->badge()
                    ->color(fn (?PluginTier $state): string => $state?->color() ?? 'gray')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (PluginStatus $state): string => match ($state) {
                        PluginStatus::Pending => 'warning',
                        PluginStatus::Approved => 'success',
                        PluginStatus::Rejected => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('featured')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\IconColumn::make('satis_synced_at')
                    ->label('Satis')
                    ->boolean()
                    ->getStateUsing(fn (Plugin $record): bool => $record->isSatisSynced())
                    ->tooltip(fn (Plugin $record): ?string => $record->satis_synced_at?->diffForHumans())
                    ->visible(fn (): bool => true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PluginStatus::class),
                Tables\Filters\SelectFilter::make('type')
                    ->options(PluginType::class),
                Tables\Filters\TernaryFilter::make('featured'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PricesRelationManager::class,
            RelationManagers\LicensesRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlugins::route('/'),
            'edit' => Pages\EditPlugin::route('/{record}/edit'),
        ];
    }
}
