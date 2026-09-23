<?php

namespace App\Filament\Pages;

use App\Filament\Resources\UserResource;
use App\Services\CompanyAggregator;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Companies extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Companies';

    protected static ?string $title = 'Companies';

    protected static ?int $navigationSort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->records(function (
                ?string $search,
                ?string $sortColumn,
                ?string $sortDirection,
                int $page,
                int $recordsPerPage,
            ): LengthAwarePaginator {
                $records = app(CompanyAggregator::class)->aggregate();

                $records = $records
                    ->when(
                        filled($search),
                        fn (Collection $data): Collection => $data->filter(
                            fn (array $record): bool => str_contains(
                                Str::lower($record['domain']),
                                Str::lower($search),
                            ),
                        ),
                    );

                if (filled($sortColumn)) {
                    $records = $records->sortBy(
                        $sortColumn,
                        SORT_REGULAR,
                        ($sortDirection ?? 'desc') === 'desc',
                    );
                } else {
                    $records = $records->sortByDesc('users_count');
                }

                $records = $records->values();
                $total = $records->count();

                $pageItems = $records
                    ->forPage($page, $recordsPerPage)
                    ->mapWithKeys(fn (array $record): array => [$record['domain'] => $record]);

                return new LengthAwarePaginator(
                    items: $pageItems,
                    total: $total,
                    perPage: $recordsPerPage,
                    currentPage: $page,
                );
            })
            ->columns([
                TextColumn::make('domain')
                    ->label('Domain')
                    ->sortable()
                    ->copyable(),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->sortable()
                    ->numeric(),
                TextColumn::make('earliest_signup')
                    ->label('Earliest signup')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('latest_signup')
                    ->label('Latest signup')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('users_count', 'desc')
            ->searchable()
            ->actions([
                Action::make('viewUsers')
                    ->label('Users')
                    ->icon('heroicon-o-users')
                    ->color('gray')
                    ->url(fn (array $record): string => UserResource::getUrl('index', [
                        'tableSearch' => $record['domain'],
                    ])),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
