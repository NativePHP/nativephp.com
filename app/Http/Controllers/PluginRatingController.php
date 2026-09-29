<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePluginRatingRequest;
use App\Models\Plugin;
use App\Models\PluginRating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PluginRatingController extends Controller
{
    public function store(StorePluginRatingRequest $request, string $vendor, string $package): RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        $this->authorize('create', [PluginRating::class, $plugin]);

        PluginRating::submit($plugin, $request->user(), (int) $request->validated('rating'));

        return back()->with('success', 'Thanks for rating this plugin!');
    }

    public function destroy(Request $request, string $vendor, string $package): RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);
        $rating = PluginRating::findFor($plugin, $request->user());

        abort_if(! $rating, 404);

        $this->authorize('delete', $rating);

        $rating->delete();

        return back()->with('success', 'Your rating has been removed.');
    }
}
