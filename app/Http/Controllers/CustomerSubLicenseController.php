<?php

namespace App\Http\Controllers;

use App\Actions\SubLicenses\DeleteSubLicense;
use App\Actions\SubLicenses\SuspendSubLicense;
use App\Http\Requests\CreateSubLicenseRequest;
use App\Jobs\CreateAnystackSubLicenseJob;
use App\Jobs\UpdateAnystackContactAssociationJob;
use App\Models\License;
use App\Models\SubLicense;
use App\Notifications\SubLicenseAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

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
        CreateAnystackSubLicenseJob::dispatch($license, $request->name, $request->assigned_email);

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
            'assigned_email' => ['nullable', 'email', 'max:255'],
        ]);

        $oldEmail = $subLicense->assigned_email;

        $subLicense->update([
            'name' => $request->name,
            'assigned_email' => $request->assigned_email,
        ]);

        // If the email was changed and there's a new email, update the contact association
        if ($oldEmail !== $request->assigned_email && $request->assigned_email) {
            UpdateAnystackContactAssociationJob::dispatch($subLicense, $request->assigned_email);
        }

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


    public function sendEmail(string $licenseKey, SubLicense $subLicense): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        // Verify the sub-license belongs to this license
        if ($subLicense->parent_license_id !== $license->id) {
            abort(404);
        }

        // Verify the sub-license has an assigned email
        if (! $subLicense->assigned_email) {
            return redirect()->route('customer.licenses.show', $licenseKey)
                ->withErrors(['email' => 'This sub-license does not have an assigned email address.']);
        }

        // Rate limiting: max 1 email per minute per sub-license
        $rateLimiterKey = "send-license-email.{$subLicense->id}";
        
        if (RateLimiter::tooManyAttempts($rateLimiterKey, 1)) {
            $secondsUntilAvailable = RateLimiter::availableIn($rateLimiterKey);
            
            return redirect()->route('customer.licenses.show', $licenseKey)
                ->withErrors(['rate_limit' => "Please wait {$secondsUntilAvailable} seconds before sending another email for this license."]);
        }

        // Record the attempt
        RateLimiter::hit($rateLimiterKey, 60); // 60 seconds = 1 minute

        // Send the notification
        Notification::route('mail', $subLicense->assigned_email)
            ->notify(new SubLicenseAssignment($subLicense));

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', "License details sent to {$subLicense->assigned_email} successfully!");
    }
}
