<?php

use App\Features\ShowAuthButtons;
use App\Features\ShowPlugins;
use App\Http\Controllers\ApplinksController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerLicenseController;
use App\Http\Controllers\CustomerPluginController;
use App\Http\Controllers\CustomerSubLicenseController;
use App\Http\Controllers\DeveloperOnboardingController;
use App\Http\Controllers\OpenCollectiveWebhookController;
use App\Http\Controllers\PluginDirectoryController;
use App\Http\Controllers\PluginPurchaseController;
use App\Http\Controllers\PluginWebhookController;
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

// Public plugin directory routes
Route::middleware(EnsureFeaturesAreActive::using(ShowPlugins::class))->group(function () {
    Route::get('plugins', [PluginDirectoryController::class, 'index'])->name('plugins');
    Route::get('plugins/directory', App\Livewire\PluginDirectory::class)->name('plugins.directory');
    Route::get('plugins/{plugin}', [PluginDirectoryController::class, 'show'])->name('plugins.show');
});

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

    Route::get('register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('register', [CustomerAuthController::class, 'register'])->middleware('throttle:5,1');

    Route::get('forgot-password', [CustomerAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('forgot-password', [CustomerAuthController::class, 'sendPasswordResetLink'])->name('password.email');

    Route::get('reset-password/{token}', [CustomerAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->name('password.update');

    Route::get('auth/github/login', [App\Http\Controllers\GitHubAuthController::class, 'redirect'])->name('login.github');
});

Route::post('logout', [CustomerAuthController::class, 'logout'])
    ->middleware(EnsureFeaturesAreActive::using(ShowAuthButtons::class))
    ->name('customer.logout');

// GitHub OAuth callback (no auth required - handles both login and linking)
Route::get('auth/github/callback', [App\Http\Controllers\GitHubIntegrationController::class, 'handleCallback'])->name('github.callback');

// GitHub OAuth routes (auth required)
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class)])->group(function () {
    Route::get('auth/github', [App\Http\Controllers\GitHubIntegrationController::class, 'redirectToGitHub'])->name('github.redirect');
    Route::post('customer/github/request-access', [App\Http\Controllers\GitHubIntegrationController::class, 'requestRepoAccess'])->name('github.request-access');
    Route::delete('customer/github/disconnect', [App\Http\Controllers\GitHubIntegrationController::class, 'disconnect'])->name('github.disconnect');
    Route::get('customer/github/repositories', [App\Http\Controllers\GitHubIntegrationController::class, 'repositories'])->name('github.repositories');
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

// Dashboard route
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class)])
    ->get('dashboard', [CustomerLicenseController::class, 'index'])
    ->name('dashboard');

// Customer license management routes
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class)])->prefix('customer')->name('customer.')->group(function () {
    // Redirect old licenses URL to dashboard
    Route::redirect('licenses', '/dashboard')->name('licenses');
    Route::view('integrations', 'customer.integrations')->name('integrations');
    Route::get('licenses/{licenseKey}', [CustomerLicenseController::class, 'show'])->name('licenses.show');
    Route::patch('licenses/{licenseKey}', [CustomerLicenseController::class, 'update'])->name('licenses.update');

    // Wall of Love submission
    Route::get('wall-of-love/create', [App\Http\Controllers\WallOfLoveSubmissionController::class, 'create'])->name('wall-of-love.create');

    // Showcase submissions
    Route::get('showcase', [App\Http\Controllers\CustomerShowcaseController::class, 'index'])->name('showcase.index');
    Route::get('showcase/create', [App\Http\Controllers\CustomerShowcaseController::class, 'create'])->name('showcase.create');
    Route::get('showcase/{showcase}/edit', [App\Http\Controllers\CustomerShowcaseController::class, 'edit'])->name('showcase.edit');

    // Plugin management
    Route::middleware(EnsureFeaturesAreActive::using(ShowPlugins::class))->group(function () {
        Route::get('plugins', [CustomerPluginController::class, 'index'])->name('plugins.index');
        Route::get('plugins/submit', [CustomerPluginController::class, 'create'])->name('plugins.create');
        Route::post('plugins', [CustomerPluginController::class, 'store'])->name('plugins.store');
        Route::get('plugins/{plugin}', [CustomerPluginController::class, 'show'])->name('plugins.show');
        Route::patch('plugins/{plugin}', [CustomerPluginController::class, 'update'])->name('plugins.update');
        Route::post('plugins/{plugin}/resubmit', [CustomerPluginController::class, 'resubmit'])->name('plugins.resubmit');
        Route::post('plugins/{plugin}/logo', [CustomerPluginController::class, 'updateLogo'])->name('plugins.logo.update');
        Route::delete('plugins/{plugin}/logo', [CustomerPluginController::class, 'deleteLogo'])->name('plugins.logo.delete');
        Route::patch('plugins/{plugin}/price', [CustomerPluginController::class, 'updatePrice'])->name('plugins.price.update');
        Route::patch('plugins/display-name', [CustomerPluginController::class, 'updateDisplayName'])->name('plugins.display-name');
    });

    // Billing portal
    Route::get('billing-portal', function (Illuminate\Http\Request $request) {
        $user = $request->user();

        // Check if user exists in Stripe, create if they don't
        if (! $user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        return $user->redirectToBillingPortal(route('dashboard'));
    })->name('billing-portal');

    // Sub-license management routes
    Route::post('licenses/{licenseKey}/sub-licenses', [CustomerSubLicenseController::class, 'store'])->name('licenses.sub-licenses.store');
    Route::patch('licenses/{licenseKey}/sub-licenses/{subLicense}', [CustomerSubLicenseController::class, 'update'])->name('licenses.sub-licenses.update');
    Route::delete('licenses/{licenseKey}/sub-licenses/{subLicense}', [CustomerSubLicenseController::class, 'destroy'])->name('licenses.sub-licenses.destroy');
    Route::patch('licenses/{licenseKey}/sub-licenses/{subLicense}/suspend', [CustomerSubLicenseController::class, 'suspend'])->name('licenses.sub-licenses.suspend');
    Route::post('licenses/{licenseKey}/sub-licenses/{subLicense}/send-email', [CustomerSubLicenseController::class, 'sendEmail'])->name('licenses.sub-licenses.send-email');
});

Route::get('.well-known/assetlinks.json', [ApplinksController::class, 'assetLinks']);

Route::post('webhooks/plugins/{secret}', PluginWebhookController::class)->name('webhooks.plugins');

// Plugin purchase routes
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class), EnsureFeaturesAreActive::using(ShowPlugins::class)])->group(function () {
    Route::get('plugins/{plugin}/purchase', [PluginPurchaseController::class, 'show'])->name('plugins.purchase.show');
    Route::post('plugins/{plugin}/purchase', [PluginPurchaseController::class, 'checkout'])->name('plugins.purchase.checkout');
    Route::get('plugins/{plugin}/purchase/success', [PluginPurchaseController::class, 'success'])->name('plugins.purchase.success');
    Route::get('plugins/{plugin}/purchase/status/{sessionId}', [PluginPurchaseController::class, 'status'])->name('plugins.purchase.status');
    Route::get('plugins/{plugin}/purchase/cancel', [PluginPurchaseController::class, 'cancel'])->name('plugins.purchase.cancel');
});

// Bundle routes (public)
Route::middleware(EnsureFeaturesAreActive::using(ShowPlugins::class))->group(function () {
    Route::get('bundles/{bundle:slug}', [BundleController::class, 'show'])->name('bundles.show');
});

// Cart routes (public - allows guest cart)
Route::middleware(EnsureFeaturesAreActive::using(ShowPlugins::class))->group(function () {
    Route::get('cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('cart/add/{plugin}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('cart/remove/{plugin}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('cart/bundle/{bundle:slug}', [CartController::class, 'addBundle'])->name('cart.bundle.add');
    Route::post('cart/bundle/{bundle:slug}/exchange', [CartController::class, 'exchangeForBundle'])->name('cart.bundle.exchange');
    Route::delete('cart/bundle/{bundle:slug}', [CartController::class, 'removeBundle'])->name('cart.bundle.remove');
    Route::delete('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::get('cart/success', [CartController::class, 'success'])->name('cart.success')->middleware('auth');
    Route::get('cart/status/{sessionId}', [CartController::class, 'status'])->name('cart.status')->middleware('auth');
    Route::get('cart/cancel', [CartController::class, 'cancel'])->name('cart.cancel');
});

// Developer onboarding routes
Route::middleware(['auth', EnsureFeaturesAreActive::using(ShowAuthButtons::class), EnsureFeaturesAreActive::using(ShowPlugins::class)])->prefix('customer/developer')->name('customer.developer.')->group(function () {
    Route::get('onboarding', [DeveloperOnboardingController::class, 'show'])->name('onboarding');
    Route::post('onboarding/start', [DeveloperOnboardingController::class, 'start'])->name('onboarding.start');
    Route::get('onboarding/return', [DeveloperOnboardingController::class, 'return'])->name('onboarding.return');
    Route::get('onboarding/refresh', [DeveloperOnboardingController::class, 'refresh'])->name('onboarding.refresh');
    Route::get('dashboard', [DeveloperOnboardingController::class, 'dashboard'])->name('dashboard');
});
