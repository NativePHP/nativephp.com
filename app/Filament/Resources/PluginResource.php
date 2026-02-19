<?php

namespace App\Filament\Resources;

use App\Enums\PluginStatus;
use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Filament\Resources\PluginResource\Pages;
use App\Filament\Resources\PluginResource\RelationManagers;
use App\Jobs\ReviewPluginRepository;
use App\Jobs\SyncPlugin;
use App\Models\Plugin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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

                Forms\Components\Section::make('Review Checks')
                    ->schema([
                        Forms\Components\Placeholder::make('reviewed_at_display')
                            ->label('Last Reviewed')
                            ->content(fn (?Plugin $record) => $record?->reviewed_at?->diffForHumans() ?? 'Never'),

                        Forms\Components\Placeholder::make('review_ios')
                            ->label('iOS Support')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['supports_ios'] ?? false) ? '✅ Found' : '❌ Missing'),

                        Forms\Components\Placeholder::make('review_android')
                            ->label('Android Support')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['supports_android'] ?? false) ? '✅ Found' : '❌ Missing'),

                        Forms\Components\Placeholder::make('review_js')
                            ->label('JS Support')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['supports_js'] ?? false) ? '✅ Found' : '❌ Missing'),

                        Forms\Components\Placeholder::make('review_email')
                            ->label('Support Email')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['has_support_email'] ?? false)
                                ? '✅ '.($record->review_checks['support_email'] ?? '')
                                : '❌ Missing'),

                        Forms\Components\Placeholder::make('review_sdk')
                            ->label('Requires nativephp/mobile')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['requires_mobile_sdk'] ?? false)
                                ? '✅ '.($record->review_checks['mobile_sdk_constraint'] ?? '')
                                : '❌ Missing'),

                        Forms\Components\Placeholder::make('review_ios_min_version')
                            ->label('iOS min_version')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['has_ios_min_version'] ?? false)
                                ? '✅ '.($record->review_checks['ios_min_version'] ?? '')
                                : '❌ Missing'),

                        Forms\Components\Placeholder::make('review_android_min_version')
                            ->label('Android min_version')
                            ->content(fn (?Plugin $record) => ($record?->review_checks['has_android_min_version'] ?? false)
                                ? '✅ '.($record->review_checks['android_min_version'] ?? '')
                                : '❌ Missing'),
                    ])
                    ->columns(4)
                    ->visible(fn (?Plugin $record) => $record?->review_checks !== null),

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

                Tables\Columns\IconColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->boolean()
                    ->getStateUsing(fn (Plugin $record): bool => $record->reviewed_at !== null)
                    ->tooltip(fn (Plugin $record): ?string => $record->reviewed_at?->diffForHumans())
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('resync')
                        ->label('Re-sync from GitHub')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->visible(fn (Plugin $record): bool => $record->repository_url !== null)
                        ->requiresConfirmation()
                        ->modalHeading('Re-sync Plugin')
                        ->modalDescription(fn (Plugin $record): string => "This will re-fetch the README, composer.json, nativephp.json, license, and latest version from GitHub for '{$record->name}'.")
                        ->action(function (Plugin $record): void {
                            SyncPlugin::dispatch($record);

                            Notification::make()
                                ->title('Sync queued')
                                ->body("A background sync has been queued for '{$record->name}'.")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('runReviewChecks')
                        ->label('Run Review Checks')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('primary')
                        ->visible(fn (Plugin $record): bool => $record->repository_url !== null)
                        ->requiresConfirmation()
                        ->modalHeading('Run Review Checks')
                        ->modalDescription(fn (Plugin $record): string => "This will run automated checks for '{$record->name}'.")
                        ->action(function (Plugin $record): void {
                            $checks = (new ReviewPluginRepository($record))->handle();

                            if (empty($checks)) {
                                Notification::make()
                                    ->title('Review checks failed')
                                    ->body('Could not fetch repository data.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $passed = collect($checks)->only([
                                'supports_ios', 'supports_android', 'supports_js',
                                'has_support_email', 'requires_mobile_sdk',
                                'has_ios_min_version', 'has_android_min_version',
                            ])->filter()->count();

                            Notification::make()
                                ->title("Review checks complete ({$passed}/7 passed)")
                                ->success()
                                ->send();
                        }),
                ])
                    ->icon('heroicon-o-bolt')
                    ->color('primary')
                    ->tooltip('Quick Actions'),

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
