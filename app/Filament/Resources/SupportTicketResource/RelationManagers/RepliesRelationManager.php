<?php

namespace App\Filament\Resources\SupportTicketResource\RelationManagers;

use App\Models\SupportTicket\Reply;
use App\Notifications\SupportTicketReplied;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    protected static ?string $title = 'Replies';

    protected static bool $shouldSkipAuthorization = true;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->maxLength(5000)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('note')
                    ->label('Internal note (not visible to user)')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->limit(80)
                    ->tooltip(fn ($record) => $record->message),

                Tables\Columns\IconColumn::make('note')
                    ->label('Note')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Reply')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function (Reply $record): void {
                        if ($record->note) {
                            return;
                        }

                        $ticket = $this->getOwnerRecord();

                        if ($ticket->user_id !== auth()->id()) {
                            $ticket->user->notify(new SupportTicketReplied($ticket, $record));
                        }
                    }),
            ])
            ->actions([
                //
            ]);
    }
}
