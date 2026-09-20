<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PluginReportCategory;
use App\Http\Requests\StorePluginReportRequest;
use App\Models\Plugin;
use App\Models\PluginReport;
use Illuminate\Http\RedirectResponse;

final class PluginReportController extends Controller
{
    public function store(StorePluginReportRequest $request, string $vendor, string $package): RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        $this->authorize('create', [PluginReport::class, $plugin]);

        $user = $request->user();

        if (PluginReport::hasOpenFor($plugin, $user)) {
            return back()->with('error', 'You already have an open report for this plugin — our team will follow up soon.');
        }

        PluginReport::file(
            $plugin,
            $user,
            PluginReportCategory::from($request->validated('category')),
            $request->validated('message')
        );

        return back()->with('success', 'Thanks — your report has been sent to the NativePHP team.');
    }
}
