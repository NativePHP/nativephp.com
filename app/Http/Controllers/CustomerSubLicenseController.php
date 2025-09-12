<?php

namespace App\Http\Controllers;

use App\Actions\SubLicenses\DeleteSubLicense;
use App\Actions\SubLicenses\SuspendSubLicense;
use App\Actions\SubLicenses\UnsuspendSubLicense;
use App\Http\Requests\CreateSubLicenseRequest;
use App\Jobs\CreateAnystackSubLicenseJob;
use App\Models\License;
use App\Models\SubLicense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSubLicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(CreateSubLicenseRequest $request, string $licenseKey): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        if (! $license->canCreateSubLicense()) {
            return redirect()->route('customer.licenses.show', $licenseKey)->withErrors([
                'sub_license' => 'Unable to create sub-license. Check license status and limits.',
            ]);
        }

        // Dispatch job to create sub-license in Anystack and then locally
        CreateAnystackSubLicenseJob::dispatch($license, $request->name);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'Sub-license is being created. You will receive an email notification when it\'s ready.');
    }

    public function update(Request $request, string $licenseKey, SubLicense $subLicense): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        // Verify the sub-license belongs to this license
        if ($subLicense->parent_license_id !== $license->id) {
            abort(404);
        }

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $subLicense->update([
            'name' => $request->name,
        ]);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'Sub-license updated successfully!');
    }

    public function destroy(string $licenseKey, SubLicense $subLicense): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        // Verify the sub-license belongs to this license
        if ($subLicense->parent_license_id !== $license->id) {
            abort(404);
        }

        app(DeleteSubLicense::class)->handle($subLicense);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'Sub-license deleted successfully!');
    }

    public function suspend(string $licenseKey, SubLicense $subLicense): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        // Verify the sub-license belongs to this license
        if ($subLicense->parent_license_id !== $license->id) {
            abort(404);
        }

        app(SuspendSubLicense::class)->handle($subLicense);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'Sub-license suspended successfully!');
    }

    public function unsuspend(string $licenseKey, SubLicense $subLicense): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        // Verify the sub-license belongs to this license
        if ($subLicense->parent_license_id !== $license->id) {
            abort(404);
        }

        app(UnsuspendSubLicense::class)->handle($subLicense);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'Sub-license unsuspended successfully!');
    }
}
