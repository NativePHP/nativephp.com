<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use App\SupportTicket\Status;
use Filament\Actions;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Support Tickets';

    protected static ?string $pluralModelLabel = 'Support Tickets';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->schema([
                Section::make('Ticket Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('mask')
                            ->label('Ticket ID'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (Status $state): string => match ($state) {
                                Status::OPEN => 'warning',
                                Status::IN_PROGRESS => 'info',
                                Status::ON_HOLD => 'gray',
                                Status::RESPONDED => 'success',
                                Status::CLOSED => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('product')
                            ->label('Product'),
                        Infolists\Components\TextEntry::make('issue_type')
                            ->label('Issue Type')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User')
                            ->formatStateUsing(fn (SupportTicket $record): string => ($record->user->name ?? '').' ('.$record->user->email.')')
                            ->url(fn (SupportTicket $record): string => UserResource::getUrl('edit', ['record' => $record->user_id])),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpan(1),

                Section::make('Context')
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')
                            ->label('Subject')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('message')
                            ->label('Message')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mask')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->formatStateUsing(fn (SupportTicket $record): string => ($record->user->name ?? '').' ('.$record->user->email.')')
                    ->searchable(query: fn ($query, string $search) => $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
                    ->sortable(),

                Tables\Columns\TextColumn::make('product')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (Status $state): string => match ($state) {
                        Status::OPEN => 'warning',
                        Status::IN_PROGRESS => 'info',
                        Status::ON_HOLD => 'gray',
                        Status::RESPONDED => 'success',
                        Status::CLOSED => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(Status::cases())->mapWithKeys(fn (Status $s) => [$s->value => $s->name])),
                Tables\Filters\SelectFilter::make('product')
                    ->options([
                        'mobile' => 'Mobile',
                        'desktop' => 'Desktop',
                        'bifrost' => 'Bifrost',
                        'nativephp.com' => 'NativePHP.com',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
        ];
    }
}
