<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerLicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        $licenses = $user->licenses()->orderBy('created_at', 'desc')->get();

        return view('customer.licenses.index', compact('licenses'));
    }

    public function show(string $licenseKey): View
    {
        $user = Auth::user();
        $license = $user->licenses()->with('subLicenses')->where('key', $licenseKey)->firstOrFail();

        return view('customer.licenses.show', compact('license'));
    }

    public function update(Request $request, string $licenseKey): RedirectResponse
    {
        $user = Auth::user();
        $license = $user->licenses()->where('key', $licenseKey)->firstOrFail();

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $license->update([
            'name' => $request->name,
        ]);

        return redirect()->route('customer.licenses.show', $licenseKey)
            ->with('success', 'License name updated successfully!');
    }
}
