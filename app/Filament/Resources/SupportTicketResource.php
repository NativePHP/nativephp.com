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
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Section::make('Ticket Details')
                    ->inlineLabel()
                    ->columns(1)
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
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('User')
                            ->formatStateUsing(fn (SupportTicket $record): string => trim(($record->user->name ?? '').' ('.$record->user->email.')'))
                            ->url(fn (SupportTicket $record): string => UserResource::getUrl('edit', ['record' => $record->user_id])),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime(),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Section::make('Context')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')
                            ->label('Subject'),
                        Infolists\Components\TextEntry::make('message')
                            ->label('Message')
                            ->formatStateUsing(fn (?string $state): ?HtmlString => $state === null
                                ? null
                                : new HtmlString(self::renderTicketMessage($state)))
                            ->html(),
                        Infolists\Components\TextEntry::make('attachments')
                            ->label('Attachments')
                            ->formatStateUsing(function (SupportTicket $record): HtmlString {
                                $attachments = $record->attachments;

                                if (empty($attachments)) {
                                    return new HtmlString('<span style="color: #9ca3af;">None</span>');
                                }

                                $links = collect($attachments)->map(function (array $attachment, int $index) use ($record): string {
                                    $url = route('customer.support.tickets.attachment', [$record, $index]);

                                    return '<a href="'.e($url).'" target="_blank" style="color: #2563eb; text-decoration: underline;">'.e($attachment['name']).'</a>';
                                });

                                return new HtmlString($links->implode('<br>'));
                            })
                            ->html(),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
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

                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->formatStateUsing(fn (SupportTicket $record): string => trim(($record->user->name ?? '').' ('.$record->user->email.')'))
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

    public static function renderTicketMessage(string $message): string
    {
        $html = Str::markdown(self::convertAsciiTablesToHtml($message), [
            'renderer' => [
                'soft_break' => "<br />\n",
            ],
        ]);

        return str_replace('<p>', '<p style="margin: 0 0 1rem 0;">', $html);
    }

    protected static function convertAsciiTablesToHtml(string $message): string
    {
        $lines = preg_split('/\R/', $message) ?: [];
        $result = [];
        $buffer = [];

        $flush = function () use (&$result, &$buffer): void {
            if ($buffer === []) {
                return;
            }

            $rendered = self::renderAsciiTable($buffer);

            if ($rendered === null) {
                foreach ($buffer as $bufferedLine) {
                    $result[] = $bufferedLine;
                }
            } else {
                $result[] = '';
                $result[] = $rendered;
                $result[] = '';
            }

            $buffer = [];
        };

        foreach ($lines as $line) {
            if (preg_match('/^\s*[+|]/', $line)) {
                $buffer[] = $line;

                continue;
            }

            $flush();
            $result[] = $line;
        }

        $flush();

        return implode("\n", $result);
    }

    protected static function renderAsciiTable(array $lines): ?string
    {
        $rows = [];
        $separatorAfterRow = [];

        foreach ($lines as $line) {
            $trimmed = ltrim($line);

            if (str_starts_with($trimmed, '+')) {
                $separatorAfterRow[count($rows)] = true;

                continue;
            }

            if (str_starts_with($trimmed, '|')) {
                $rows[] = self::splitAsciiTableRow($trimmed);
            }
        }

        if ($rows === []) {
            return null;
        }

        $hasHeader = count($rows) > 1 && isset($separatorAfterRow[1]);

        $tableStyle = 'border-collapse: collapse; width: auto; margin: 0 0 1rem 0; border: 1px solid rgba(127, 127, 127, 0.25);';
        $cellStyle = 'padding: 0.25rem 0.75rem; border: 1px solid rgba(127, 127, 127, 0.2); text-align: left; vertical-align: top;';
        $headerCellStyle = $cellStyle.' font-weight: 600; background: rgba(127, 127, 127, 0.12);';
        $stripeStyle = 'background: rgba(127, 127, 127, 0.06);';

        $html = '<table style="'.$tableStyle.'">';

        if ($hasHeader) {
            $html .= '<thead><tr>';
            foreach ($rows[0] as $cell) {
                $html .= '<th style="'.$headerCellStyle.'">'.e($cell).'</th>';
            }
            $html .= '</tr></thead>';
            $bodyRows = array_slice($rows, 1);
        } else {
            $bodyRows = $rows;
        }

        $html .= '<tbody>';
        foreach ($bodyRows as $index => $row) {
            $rowStyle = $index % 2 === 1 ? ' style="'.$stripeStyle.'"' : '';
            $html .= '<tr'.$rowStyle.'>';
            foreach ($row as $cell) {
                $html .= '<td style="'.$cellStyle.'">'.e($cell).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * @return list<string>
     */
    protected static function splitAsciiTableRow(string $line): array
    {
        $line = trim($line);
        $line = trim($line, '|');

        return array_map('trim', explode('|', $line));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
        ];
    }
}
