<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ImportAnystackContacts implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $maxExceptions = 1;

    public function __construct(
        public int $page,
    ) {}

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            new RateLimited('anystack'),
        ];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(20);
    }

    public function handle(): void
    {
        $response = Http::acceptJson()
            ->withToken(config('services.anystack.key'))
            ->get("https://api.anystack.sh/v1/contacts?page={$this->page}")
            ->json();

        collect($response['data'])
            ->each(function (array $contact): void {
                dispatch(new UpsertUserFromAnystackContact($contact));
            });

        if (filled($response['links']['next'])) {
            $this->batch()?->add(new self($this->page + 1));
        }
    }
}
