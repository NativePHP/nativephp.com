<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CustomerShowcaseController extends Controller
{
    public function index(Request $request): View
    {
        $showcases = Showcase::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('customer.showcase.index', compact('showcases'));
    }

    public function create(): View
    {
        return view('customer.showcase.create');
    }

    public function edit(Showcase $showcase): View
    {
        abort_if($showcase->user_id !== auth()->id(), 403);

        return view('customer.showcase.edit', compact('showcase'));
    }
}
