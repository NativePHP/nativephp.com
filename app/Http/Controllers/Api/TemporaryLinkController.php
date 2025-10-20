<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
                'email' => $validated['email'],
            ]
        );

        return response()->json(['url' => $url]);
    }
}
