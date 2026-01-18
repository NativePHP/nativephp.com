<?php

namespace App\Filament\Resources;

use App\Enums\PluginType;
use App\Filament\Resources\PluginBundleResource\Pages;
use App\Filament\Resources\PluginBundleResource\RelationManagers;
use App\Models\Plugin;
use App\Models\PluginBundle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PluginBundleResource extends Resource
{
    protected static ?string $model = PluginBundle::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Bundles';

    protected static ?string $navigationGroup = 'Plugins';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bundle Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Bundle Logo')
                            ->image()
                            ->disk('public')
                            ->directory('bundle-logos')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('256')
                            ->imageResizeTargetHeight('256'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Bundle Price (in cents)')
                            ->required()
                            ->numeric()
                            ->minValue(100)
                            ->helperText('Enter price in cents. E.g., 4999 = $49.99'),

                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD',
                            ])
                            ->default('USD')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Included Plugins')
                    ->schema([
                        Forms\Components\Select::make('plugins')
                            ->relationship(
                                'plugins',
                                'name',
                                fn ($query) => $query->approved()->where('type', PluginType::Paid)->whereHas('activePrice')
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->getOptionLabelFromRecordUsing(function (Plugin $record) {
                                $price = $record->activePrice ? '$'.number_format($record->activePrice->amount / 100, 2) : 'No price';

                                return "{$record->name} ({$price})";
                            })
                            ->helperText('Select paid plugins to include in this bundle.')
                            ->optionsLimit(50),
                    ]),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Bundle will only be visible when active and published.'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Show this bundle prominently in the bundles section.'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->helperText('Leave empty to keep as draft. Set future date to schedule.'),
                    ])
                    ->columns(3),
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
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plugins_count')
                    ->label('Plugins')
                    ->counts('plugins')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Bundle Price')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('retail_value')
                    ->label('Retail Value')
                    ->getStateUsing(fn (PluginBundle $record): string => $record->formatted_retail_value),

                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Discount')
                    ->getStateUsing(fn (PluginBundle $record): string => $record->discount_percent.'%')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_featured')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('viewListing')
                        ->label('View Listing Page')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->url(fn (PluginBundle $record) => route('bundles.show', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (PluginBundle $record) => $record->is_active && $record->published_at?->isPast()),
                ])
                    ->label('More')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PluginsRelationManager::class,
            RelationManagers\LicensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPluginBundles::route('/'),
            'create' => Pages\CreatePluginBundle::route('/create'),
            'view' => Pages\ViewPluginBundle::route('/{record}'),
            'edit' => Pages\EditPluginBundle::route('/{record}/edit'),
        ];
    }
}
