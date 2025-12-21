<?php

use App\Features\ShowAuthButtons;
use App\Http\Controllers\ApplinksController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CustomerLicenseController;
use App\Http\Controllers\CustomerSubLicenseController;
use App\Http\Controllers\OpenCollectiveWebhookController;
use App\Http\Controllers\ShowBlogController;
use App\Http\Controllers\ShowDocumentationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('newsletter', 'https://simonhamp.mailcoach.app/nativephp');
Route::redirect('phpverse-2025', 'https://lp.jetbrains.com/phpverse-2025');
Route::redirect('docs/1/getting-started/sponsoring', '/sponsor');
Route::redirect('docs/desktop/1/getting-started/sponsoring', '/sponsor');
Route::redirect('discord', 'https://discord.gg/nativephp');
Route::redirect('bifrost', 'https://bifrost.nativephp.com');
Route::redirect('mobile', 'pricing');
Route::redirect('ios', 'pricing');
Route::redirect('t-shirt', 'pricing');
Route::redirect('tshirt', 'pricing');

// Webhook routes (must be outside web middleware for CSRF bypass)
Route::post('opencollective/contribution', [OpenCollectiveWebhookController::class, 'handle'])->name('opencollective.webhook');

// OpenCollective donation claim route
Route::get('opencollective/claim', App\Livewire\ClaimDonationLicense::class)->name('opencollective.claim');

Route::view('/', 'welcome')->name('welcome');
Route::view('pricing', 'pricing')->name('pricing');
Route::view('alt-pricing', 'alt-pricing')->name('alt-pricing')->middleware('signed');
Route::view('wall-of-love', 'wall-of-love')->name('wall-of-love');
Route::view('brand', 'brand')->name('brand');
Route::get('showcase/{platform?}', [App\Http\Controllers\ShowcaseController::class, 'index'])
    ->where('platform', 'mobile|desktop')
    ->name('showcase');
Route::view('laracon-us-2025-giveaway', 'laracon-us-2025-giveaway')->name('laracon-us-2025-giveaway');
Route::view('privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('terms-of-service', 'terms-of-service')->name('terms-of-service');
Route::view('partners', 'partners')->name('partners');
Route::view('build-my-app', 'build-my-app')->name('build-my-app');
Route::view('sponsor', 'sponsoring')->name('sponsoring');
Route::view('vs-react-native-expo', 'vs-react-native-expo')->name('vs-react-native-expo');
Route::view('vs-flutter', 'vs-flutter')->name('vs-flutter');

Route::get('blog', [ShowBlogController::class, 'index'])->name('blog');
Route::get('blog/{article}', [ShowBlogController::class, 'show'])->name('article');

Route::get('docs/{platform}/{version}/{page}.md', [ShowDocumentationController::class, 'serveRawMarkdown'])
    ->where('page', '(.*)')
    ->where('platform', '[a-z]+')
    ->where('version', '[0-9]+')
    ->name('docs.raw');

Route::get('docs/{platform}/{version}/{page?}', ShowDocumentationController::class)
    ->where('page', '(.*)')
    ->where('platform', '[a-z]+')
    ->where('version', '[0-9]+')
    ->name('docs.show');

// Forward platform requests without version to the latest version
Route::get('docs/{platform}/{page?}', function (string $platform, $page = null) {
    $page ??= 'getting-started/introduction';

    // Find the latest version for this platform
    $docsPath = resource_path('views/docs/'.$platform);

    if (! is_dir($docsPath)) {
        abort(404);
    }

    $versions = collect(scandir($docsPath))
        ->filter(fn ($dir) => is_numeric($dir))
        ->sort()
        ->values();

    $latestVersion = $versions->last() ?? '1';

    return redirect("/docs/{$platform}/{$latestVersion}/{$page}", 301);
})
    ->where('platform', 'desktop|mobile')
    ->where('page', '.*')
    ->name('docs.latest');

// Forward unversioned requests to the latest version
Route::get('docs/{page?}', function ($page = null) {
    $page ??= 'introduction';
    $version = session('viewing_docs_version', '1');
    $platform = session('viewing_docs_platform', 'mobile');

    $referer = request()->header('referer');

    // If coming from elsewhere in the docs, match the current version being viewed
    if (
        parse_url($referer, PHP_URL_HOST) === parse_url(url('/'), PHP_URL_HOST)
        && str($referer)->contains('/docs/')
    ) {
        $path = Str::after($referer, url('/docs/'));
        $path = ltrim($path, '/');
        $segments = explode('/', $path);

        if (count($segments) >= 2 && in_array($segments[0], ['desktop', 'mobile']) && is_numeric($segments[1])) {
            $platform = $segments[0];
            $version = $segments[1];
        }
    }

    return redirect()->route('docs.show', [
        'platform' => $platform,
        'version' => $version,
        'page' => $page,
    ]);
})->name('docs')->where('page', '.*');

Route::get('order/{checkoutSessionId}', App\Livewire\OrderSuccess::class)->name('order.success');

// License renewal routes
Route::get('license/{license:key}/renewal/success', App\Livewire\LicenseRenewalSuccess::class)->name('license.renewal.success');
Route::get('license/{license}/renewal', [App\Http\Controllers\LicenseRenewalController::class, 'show'])->name('license.renewal');
Route::post('license/{license}/renewal/checkout', [App\Http\Controllers\LicenseRenewalController::class, 'createCheckoutSession'])->name('license.renewal.checkout');

// Customer authentication routes
Route::middleware(['guest'])->group(function () {
    Route::get('login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('login', [CustomerAuthController::class, 'login']);

    Route::get('forgot-password', [CustomerAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('forgot-password', [CustomerAuthController::class, 'sendPasswordResetLink'])->name('password.email');

    Route::get('reset-password/{token}', [CustomerAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('logout', [CustomerAuthController::class, 'logout'])
    ->middleware(EnsureFeaturesAreActive::using(ShowAuthButtons::class))
    ->name('customer.logout');

// GitHub OAuth routes
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class)])->group(function () {
    Route::get('auth/github', [App\Http\Controllers\GitHubIntegrationController::class, 'redirectToGitHub'])->name('github.redirect');
    Route::get('auth/github/callback', [App\Http\Controllers\GitHubIntegrationController::class, 'handleCallback'])->name('github.callback');
    Route::post('customer/github/request-access', [App\Http\Controllers\GitHubIntegrationController::class, 'requestRepoAccess'])->name('github.request-access');
    Route::delete('customer/github/disconnect', [App\Http\Controllers\GitHubIntegrationController::class, 'disconnect'])->name('github.disconnect');
});

// Discord OAuth routes
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class)])->group(function () {
    Route::get('auth/discord', [App\Http\Controllers\DiscordIntegrationController::class, 'redirectToDiscord'])->name('discord.redirect');
    Route::get('auth/discord/callback', [App\Http\Controllers\DiscordIntegrationController::class, 'handleCallback'])->name('discord.callback');
    Route::delete('customer/discord/disconnect', [App\Http\Controllers\DiscordIntegrationController::class, 'disconnect'])->name('discord.disconnect');
});

Route::get('callback', function (Illuminate\Http\Request $request) {
    $url = $request->query('url');

    if ($url && ! str_starts_with($url, 'http')) {
        return redirect()->away($url.'?token='.uuid_create());
    }

    return response('Goodbye');
})->name('callback');

// Customer license management routes
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class)])->prefix('customer')->name('customer.')->group(function () {
    Route::get('licenses', [CustomerLicenseController::class, 'index'])->name('licenses');
    Route::view('integrations', 'customer.integrations')->name('integrations');
    Route::get('licenses/{licenseKey}', [CustomerLicenseController::class, 'show'])->name('licenses.show');
    Route::patch('licenses/{licenseKey}', [CustomerLicenseController::class, 'update'])->name('licenses.update');

    // Wall of Love submission
    Route::get('wall-of-love/create', [App\Http\Controllers\WallOfLoveSubmissionController::class, 'create'])->name('wall-of-love.create');

    // Showcase submissions
    Route::get('showcase', [App\Http\Controllers\CustomerShowcaseController::class, 'index'])->name('showcase.index');
    Route::get('showcase/create', [App\Http\Controllers\CustomerShowcaseController::class, 'create'])->name('showcase.create');
    Route::get('showcase/{showcase}/edit', [App\Http\Controllers\CustomerShowcaseController::class, 'edit'])->name('showcase.edit');

    // Billing portal
    Route::get('billing-portal', function (Illuminate\Http\Request $request) {
        $user = $request->user();

        // Check if user exists in Stripe, create if they don't
        if (! $user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        return $user->redirectToBillingPortal(route('customer.licenses'));
    })->name('billing-portal');

    // Sub-license management routes
    Route::post('licenses/{licenseKey}/sub-licenses', [CustomerSubLicenseController::class, 'store'])->name('licenses.sub-licenses.store');
    Route::patch('licenses/{licenseKey}/sub-licenses/{subLicense}', [CustomerSubLicenseController::class, 'update'])->name('licenses.sub-licenses.update');
    Route::delete('licenses/{licenseKey}/sub-licenses/{subLicense}', [CustomerSubLicenseController::class, 'destroy'])->name('licenses.sub-licenses.destroy');
    Route::patch('licenses/{licenseKey}/sub-licenses/{subLicense}/suspend', [CustomerSubLicenseController::class, 'suspend'])->name('licenses.sub-licenses.suspend');
    Route::post('licenses/{licenseKey}/sub-licenses/{subLicense}/send-email', [CustomerSubLicenseController::class, 'sendEmail'])->name('licenses.sub-licenses.send-email');
});

Route::get('.well-known/assetlinks.json', [ApplinksController::class, 'assetLinks']);
