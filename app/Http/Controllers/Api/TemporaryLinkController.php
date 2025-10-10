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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class TemporaryLinkController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'route' => 'required|string',
        ]);

        $url = URL::temporarySignedRoute(
            $validated['route'],
            now()->addMinutes($request->integer('expiration', 30)),
            [
                'email' => $validated['email']
            ]
        );

        return response()->json(['url' => $url]);
    }
}
