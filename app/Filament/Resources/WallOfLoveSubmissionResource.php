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

                Forms\Components\Section::make('Review Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approved At'),

                        Forms\Components\Select::make('approved_by')
                            ->relationship('approvedBy', 'name')
                            ->label('Approved By'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Submitted At')
                            ->content(fn (WallOfLoveSubmission $record): ?string => $record->created_at?->diffForHumans()),
                    ]),
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

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
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
