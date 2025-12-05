<?php

namespace App\Http\Controllers;

use App\Enums\PluginStatus;
use App\Http\Requests\SubmitPluginRequest;
use App\Http\Requests\UpdatePluginDescriptionRequest;
use App\Models\Plugin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerPluginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        $plugins = $user->plugins()->orderBy('created_at', 'desc')->get();

        return view('customer.plugins.index', compact('plugins'));
    }

    public function create(): View
    {
        return view('customer.plugins.create');
    }

    public function store(SubmitPluginRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->plugins()->create([
            'name' => $request->name,
            'type' => $request->type,
            'anystack_id' => $request->anystack_id,
            'status' => PluginStatus::Pending,
        ]);

        return redirect()->route('customer.plugins.index')
            ->with('success', 'Your plugin has been submitted for review!');
    }

    public function show(Plugin $plugin): View
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        return view('customer.plugins.show', compact('plugin'));
    }

    public function update(UpdatePluginDescriptionRequest $request, Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        $plugin->updateDescription($request->description, $user->id);

        return redirect()->route('customer.plugins.show', $plugin)
            ->with('success', 'Plugin description updated successfully!');
    }

    public function resubmit(Plugin $plugin): RedirectResponse
    {
        $user = Auth::user();

        // Ensure the plugin belongs to the current user
        if ($plugin->user_id !== $user->id) {
            abort(403);
        }

        // Only rejected plugins can be resubmitted
        if (! $plugin->isRejected()) {
            return redirect()->route('customer.plugins.index')
                ->with('error', 'Only rejected plugins can be resubmitted.');
        }

        $plugin->resubmit();

        return redirect()->route('customer.plugins.index')
            ->with('success', 'Your plugin has been resubmitted for review!');
    }
}
