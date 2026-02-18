<?php

namespace App\Jobs;

use App\Enums\Subscription;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ImportAnystackLicenses implements ShouldQueue
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
        $productId = Subscription::Max->anystackProductId();

        $response = Http::acceptJson()
            ->withToken(config('services.anystack.key'))
            ->get("https://api.anystack.sh/v1/products/$productId/licenses?page={$this->page}")
            ->json();

        collect($response['data'])
            ->each(function (array $license): void {
                dispatch(new UpsertLicenseFromAnystackLicense($license));
            });

        if (filled($response['links']['next'])) {
            $this->batch()?->add(new self($this->page + 1));
        }
    }
}
