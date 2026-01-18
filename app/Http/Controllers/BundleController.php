<?php

namespace App\Http\Controllers;

use App\Models\PluginBundle;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function show(PluginBundle $bundle): View
    {
        abort_unless($bundle->isActive(), 404);

        $bundle->load('plugins.activePrice', 'plugins.user');

        return view('bundle-show', [
            'bundle' => $bundle,
        ]);
    }
}
