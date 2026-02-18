<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WallOfLoveSubmissionResource\Pages;
use App\Models\WallOfLoveSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WallOfLoveSubmissionResource extends Resource
{
    protected static ?string $model = WallOfLoveSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Wall of Love';

    protected static ?string $pluralModelLabel = 'Wall of Love Submissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Submission Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('company')
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Photo')
                            ->image()
                            ->disk('public')
                            ->directory('wall-of-love-photos'),

                        Forms\Components\TextInput::make('url')
                            ->label('Website/Social URL')
                            ->url()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('testimonial')
                            ->maxLength(1000)
                            ->rows(4),
                    ]),

                Forms\Components\Section::make('Status & Promotion')
                    ->schema([
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Approved')
                            ->helperText('Approved submissions appear on the Wall of Love.')
                            ->formatStateUsing(fn (?WallOfLoveSubmission $record) => $record?->isApproved() ?? false)
                            ->dehydrated(false)
                            ->afterStateUpdated(function (bool $state, ?WallOfLoveSubmission $record): void {
                                if (! $record) {
                                    return;
                                }

                                if ($state) {
                                    $record->update([
                                        'approved_at' => now(),
                                        'approved_by' => auth()->id(),
                                    ]);
                                } else {
                                    $record->update([
                                        'approved_at' => null,
                                        'approved_by' => null,
                                        'promoted' => false,
                                        'promoted_testimonial' => null,
                                    ]);
                                }
                            })
                            ->live(),

                        Forms\Components\Toggle::make('promoted')
                            ->label('Promoted to Homepage')
                            ->helperText('Promoted submissions appear in the feedback section on the homepage.')
                            ->visible(fn (Forms\Get $get) => $get('is_approved'))
                            ->live(),

                        Forms\Components\Textarea::make('promoted_testimonial')
                            ->label('Promoted Testimonial (optional override)')
                            ->helperText('Leave empty to use the original testimonial, or enter a clipped version for the homepage.')
                            ->rows(4)
                            ->visible(fn (Forms\Get $get) => $get('is_approved') && $get('promoted')),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->height(40)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('approved_at')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                Tables\Columns\IconColumn::make('promoted')
                    ->label('Promoted')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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

                Tables\Filters\TernaryFilter::make('promoted')
                    ->label('Promoted')
                    ->placeholder('All')
                    ->trueLabel('Promoted')
                    ->falseLabel('Not Promoted'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (WallOfLoveSubmission $record) => $record->isPending())
                    ->action(fn (WallOfLoveSubmission $record) => $record->update([
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Submission')
                    ->modalDescription('Are you sure you want to approve this submission for the Wall of Love?'),

                Tables\Actions\Action::make('unapprove')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->visible(fn (WallOfLoveSubmission $record) => $record->isApproved())
                    ->action(fn (WallOfLoveSubmission $record) => $record->update([
                        'approved_at' => null,
                        'approved_by' => null,
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Unapprove Submission')
                    ->modalDescription('Are you sure you want to unapprove this submission?'),

                Tables\Actions\Action::make('promote')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (WallOfLoveSubmission $record) => $record->isApproved() && ! $record->isPromoted())
                    ->form([
                        Forms\Components\Textarea::make('promoted_testimonial')
                            ->label('Testimonial Text (optional override)')
                            ->helperText('Leave empty to use the original testimonial, or enter a clipped version.')
                            ->rows(4)
                            ->default(fn (WallOfLoveSubmission $record) => $record->testimonial),
                    ])
                    ->action(fn (WallOfLoveSubmission $record, array $data) => $record->update([
                        'promoted' => true,
                        'promoted_testimonial' => $data['promoted_testimonial'] !== $record->testimonial ? $data['promoted_testimonial'] : null,
                    ]))
                    ->modalHeading('Promote to Homepage')
                    ->modalDescription('This will display this testimonial in the feedback section on the homepage.'),

                Tables\Actions\Action::make('unpromote')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->visible(fn (WallOfLoveSubmission $record) => $record->isPromoted())
                    ->action(fn (WallOfLoveSubmission $record) => $record->update(['promoted' => false]))
                    ->requiresConfirmation()
                    ->modalHeading('Remove from Homepage')
                    ->modalDescription('This will remove this testimonial from the homepage feedback section.'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each(fn (WallOfLoveSubmission $record) => $record->update([
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
            'index' => Pages\ListWallOfLoveSubmissions::route('/'),
            // 'create' => Pages\CreateWallOfLoveSubmission::route('/create'),
            'edit' => Pages\EditWallOfLoveSubmission::route('/{record}/edit'),
        ];
    }
}
