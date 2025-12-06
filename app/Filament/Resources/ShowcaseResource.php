<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowcaseResource\Pages;
use App\Models\Showcase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShowcaseResource extends Resource
{
    protected static ?string $model = Showcase::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Showcase';

    protected static ?string $pluralModelLabel = 'Showcase Submissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('App Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Submitted By')
                            ->relationship('user', 'email')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ? "{$record->name} ({$record->email})" : $record->email)
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4),

                        Forms\Components\FileUpload::make('image')
                            ->label('Main Image')
                            ->image()
                            ->disk('public')
                            ->directory('showcase-images'),

                        Forms\Components\FileUpload::make('screenshots')
                            ->label('Screenshots (up to 5)')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('showcase-screenshots')
                            ->reorderable(),
                    ]),

                Forms\Components\Section::make('Platform Availability')
                    ->schema([
                        Forms\Components\Toggle::make('has_mobile')
                            ->label('Mobile App')
                            ->live(),

                        Forms\Components\Toggle::make('has_desktop')
                            ->label('Desktop App')
                            ->live(),

                        Forms\Components\Fieldset::make('Mobile Links')
                            ->visible(fn (Forms\Get $get) => $get('has_mobile'))
                            ->schema([
                                Forms\Components\TextInput::make('app_store_url')
                                    ->label('App Store URL')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('play_store_url')
                                    ->label('Play Store URL')
                                    ->url()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Fieldset::make('Desktop Downloads')
                            ->visible(fn (Forms\Get $get) => $get('has_desktop'))
                            ->schema([
                                Forms\Components\TextInput::make('windows_download_url')
                                    ->label('Windows Download URL')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('macos_download_url')
                                    ->label('macOS Download URL')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('linux_download_url')
                                    ->label('Linux Download URL')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Certification')
                    ->schema([
                        Forms\Components\Toggle::make('certified_nativephp')
                            ->label('Certified as built with NativePHP')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->height(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ViewColumn::make('platforms')
                    ->label('Platforms')
                    ->view('filament.tables.columns.platforms'),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('approved_at')
                    ->label('Status')
                    ->placeholder('All submissions')
                    ->trueLabel('Approved')
                    ->falseLabel('Pending')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('approved_at'),
                        false: fn (Builder $query) => $query->whereNull('approved_at'),
                    ),

                Tables\Filters\TernaryFilter::make('has_mobile')
                    ->label('Mobile App')
                    ->placeholder('All')
                    ->trueLabel('Has Mobile')
                    ->falseLabel('No Mobile'),

                Tables\Filters\TernaryFilter::make('has_desktop')
                    ->label('Desktop App')
                    ->placeholder('All')
                    ->trueLabel('Has Desktop')
                    ->falseLabel('No Desktop'),

                Tables\Filters\Filter::make('needs_re_review')
                    ->label('Needs Re-Review')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('approved_at')
                        ->whereColumn('updated_at', '>', 'approved_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Showcase $record) => $record->isPending())
                    ->action(fn (Showcase $record) => $record->update([
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Submission')
                    ->modalDescription('Are you sure you want to approve this app for the Showcase?'),

                Tables\Actions\Action::make('unapprove')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->visible(fn (Showcase $record) => $record->isApproved())
                    ->action(fn (Showcase $record) => $record->update([
                        'approved_at' => null,
                        'approved_by' => null,
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Unapprove Submission')
                    ->modalDescription('This will remove the app from the public Showcase.'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn (Showcase $record) => $record->update([
                                'approved_at' => now(),
                                'approved_by' => auth()->id(),
                            ]));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Submissions')
                        ->modalDescription('Are you sure you want to approve all selected submissions?'),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListShowcases::route('/'),
            'create' => Pages\CreateShowcase::route('/create'),
            'edit' => Pages\EditShowcase::route('/{record}/edit'),
        ];
    }
}
