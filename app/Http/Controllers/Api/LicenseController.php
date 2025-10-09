<?php

namespace App\Http\Controllers\Api;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LicenseResource;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->query('email');

        $user = User::where('email', $email)->firstOrFail();

        if ($request->has('source')) {
            $licenses = $user->licenses()->where('source', $request->query('source'))->get();
        }

        return LicenseResource::collection($licenses ?? $user->licenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'subscription' => ['required', new Enum(Subscription::class)],
        ]);

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make(Str::random(32)), // Random password
            ]
        );

        // Create the license via job
        $subscription = Subscription::from($validated['subscription']);

        CreateAnystackLicenseJob::dispatchSync(
            user: $user,
            subscription: $subscription,
            subscriptionItemId: null, // No subscription item for API-created licenses
            firstName: null, // Set to null as requested
            lastName: null,   // Set to null as requested
            source: LicenseSource::Bifrost
        );

        // Since we're using dispatchSync, the job has completed by this point
        // Find the created license
        $license = License::where('user_id', $user->id)
            ->with('user')
            ->where('policy_name', $subscription->value)
            ->where('source', LicenseSource::Bifrost)
            ->latest()
            ->firstOrFail();

        return new LicenseResource($license);
    }

    public function show(string $key)
    {
        $license = License::where('key', $key)
            ->with('user')
            ->firstOrFail();

        return new LicenseResource($license);
    }
}
