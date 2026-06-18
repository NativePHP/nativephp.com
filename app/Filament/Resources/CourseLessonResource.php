<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseLessonResource\Pages;
use App\Models\CourseLesson;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CourseLessonResource extends Resource
{
    protected static ?string $model = CourseLesson::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-play-circle';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $pluralModelLabel = 'Lessons';

    protected static ?string $modelLabel = 'Lesson';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->inlineLabel()
            ->schema([
                Forms\Components\Select::make('course_module_id')
                    ->relationship(name: 'module', titleAttribute: 'title')
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Schemas\Components\Utilities\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->rows(3),

                Forms\Components\TextInput::make('vimeo_id')
                    ->label('Vimeo ID')
                    ->maxLength(255),

                Forms\Components\TextInput::make('duration_in_seconds')
                    ->numeric()
                    ->label('Duration (seconds)'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_free')
                    ->default(false),

                Forms\Components\Toggle::make('is_published')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('module.title')
                    ->label('Module')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vimeo_id')
                    ->label('Vimeo ID')
                    ->placeholder('—'),

                Tables\Columns\ToggleColumn::make('is_free')
                    ->label('Free'),

                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Published'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseLessons::route('/'),
            'create' => Pages\CreateCourseLesson::route('/create'),
            'edit' => Pages\EditCourseLesson::route('/{record}/edit'),
        ];
    }
}
