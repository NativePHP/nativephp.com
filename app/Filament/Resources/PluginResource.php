<?php

namespace App\Filament\Resources;

use App\Enums\PluginStatus;
use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Filament\Resources\PluginResource\Pages;
use App\Filament\Resources\PluginResource\RelationManagers;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\User;
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
                    ->copyable()
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
                // Approve Action
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Plugin $record) => $record->isPending())
                    ->action(fn (Plugin $record) => $record->approve(auth()->id()))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Plugin')
                    ->modalDescription(fn (Plugin $record) => "Are you sure you want to approve '{$record->name}'?"),

                // Reject Action
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Plugin $record) => $record->isPending() || $record->isApproved())
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please explain why this plugin is being rejected...'),
                    ])
                    ->action(fn (Plugin $record, array $data) => $record->reject($data['rejection_reason'], auth()->id()))
                    ->modalHeading('Reject Plugin')
                    ->modalDescription(fn (Plugin $record) => "Are you sure you want to reject '{$record->name}'?"),

                // External Links Group
                Tables\Actions\ActionGroup::make([
                    // View Listing Page (Approved plugins only)
                    Tables\Actions\Action::make('viewListing')
                        ->label('View Listing Page')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->url(fn (Plugin $record) => route('plugins.show', $record->routeParams()))
                        ->openUrlInNewTab()
                        ->visible(fn (Plugin $record) => $record->isApproved()),

                    // Packagist Link (Free plugins only)
                    Tables\Actions\Action::make('viewPackagist')
                        ->label('View on Packagist')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn (Plugin $record) => $record->getPackagistUrl())
                        ->openUrlInNewTab()
                        ->visible(fn (Plugin $record) => $record->isFree()),

                    // GitHub Link (Free plugins only)
                    Tables\Actions\Action::make('viewGithub')
                        ->label('View on GitHub')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn (Plugin $record) => $record->getGithubUrl())
                        ->openUrlInNewTab()
                        ->visible(fn (Plugin $record) => $record->isFree()),

                    // Edit Description Action
                    Tables\Actions\Action::make('editDescription')
                        ->label('Edit Description')
                        ->icon('heroicon-o-pencil-square')
                        ->color('gray')
                        ->form([
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->required()
                                ->rows(5)
                                ->maxLength(1000)
                                ->default(fn (Plugin $record) => $record->description)
                                ->placeholder('Describe what this plugin does...'),
                        ])
                        ->action(fn (Plugin $record, array $data) => $record->updateDescription($data['description'], auth()->id()))
                        ->modalHeading('Edit Plugin Description')
                        ->modalDescription(fn (Plugin $record) => "Update the description for '{$record->name}'"),

                    Tables\Actions\Action::make('grantToUser')
                        ->label('Grant to User')
                        ->icon('heroicon-o-gift')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->label('User')
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search): array {
                                    return User::query()
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn (User $user) => [$user->id => "{$user->name} ({$user->email})"])
                                        ->toArray();
                                })
                                ->required(),
                        ])
                        ->action(function (Plugin $record, array $data): void {
                            $user = User::findOrFail($data['user_id']);

                            $existingLicense = $user->pluginLicenses()
                                ->where('plugin_id', $record->id)
                                ->exists();

                            if ($existingLicense) {
                                Notification::make()
                                    ->title('User already has a license for this plugin')
                                    ->warning()
                                    ->send();

                                return;
                            }

                            PluginLicense::create([
                                'user_id' => $user->id,
                                'plugin_id' => $record->id,
                                'price_paid' => 0,
                                'currency' => 'USD',
                                'is_grandfathered' => true,
                                'purchased_at' => now(),
                            ]);

                            $user->getPluginLicenseKey();

                            Notification::make()
                                ->title("Granted '{$record->name}' license to {$user->name}")
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Grant Plugin to User')
                        ->modalDescription(fn (Plugin $record) => "Grant '{$record->name}' to a user for free.")
                        ->modalSubmitActionLabel('Grant'),

                    Tables\Actions\ViewAction::make(),
                ])
                    ->label('More')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn (Plugin $record) => $record->approve(auth()->id()));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Plugins')
                        ->modalDescription('Are you sure you want to approve all selected plugins?'),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'view' => Pages\ViewPlugin::route('/{record}'),
        ];
    }
}
