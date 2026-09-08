<?php

namespace App\Services;

use App\Ai\Agents\CompanyDomainAnalyst;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CompanyDomainSynopsisGenerator
{
    public const SITE_TIMEOUT_SECONDS = 5;

    public const SITE_EXCERPT_CHARS = 4000;

    /**
     * Build a 30–50 word AI synopsis for a newly registered company domain.
     *
     * Soft-fails: any HTTP/AI error returns null and logs a warning so signup
     * and the basic accounts email are never blocked.
     */
    public function generate(User $user, string $domain): ?string
    {
        try {
            $siteExcerpt = $this->fetchSiteExcerpt($domain);

            $prompt = $this->buildPrompt($user, $domain, $siteExcerpt);

            $response = CompanyDomainAnalyst::make()->prompt($prompt);
            $synopsis = trim((string) $response);

            if ($synopsis === '') {
                Log::warning('Company domain synopsis empty', [
                    'domain' => $domain,
                    'user_id' => $user->id,
                ]);

                return null;
            }

            return $synopsis;
        } catch (Throwable $e) {
            report($e);

            Log::warning('Company domain synopsis failed', [
                'domain' => $domain,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return null;
        }
    }

    public function fetchSiteExcerpt(string $domain): ?string
    {
        try {
            $response = Http::timeout(self::SITE_TIMEOUT_SECONDS)
                ->withHeaders([
                    'User-Agent' => 'NativePHPCompanySynopsis/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get('https://'.$domain);

            if (! $response->successful()) {
                Log::warning('Company domain site fetch unsuccessful', [
                    'domain' => $domain,
                    'status' => $response->status(),
                ]);

                return null;
            }

            return $this->htmlToExcerpt($response->body());
        } catch (Throwable $e) {
            Log::warning('Company domain site fetch failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return null;
        }
    }

    public function htmlToExcerpt(string $html): ?string
    {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html) ?? $html;
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $html) ?? $html;
        $html = preg_replace('/<noscript\b[^>]*>.*?<\/noscript>/is', ' ', $html) ?? $html;

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        return Str::limit($text, self::SITE_EXCERPT_CHARS, '');
    }

    public function buildPrompt(User $user, string $domain, ?string $siteExcerpt): string
    {
        $excerpt = filled($siteExcerpt)
            ? $siteExcerpt
            : '(no site excerpt available)';

        return <<<PROMPT
Write the signup synopsis for this registration.

Name: {$user->name}
Email: {$user->email}
Domain: {$domain}
Company site excerpt from https://{$domain}:
{$excerpt}
PROMPT;
    }
}
