<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use Illuminate\Contracts\View\View;

class ShowcaseController extends Controller
{
    public function index(?string $platform = null): View
    {
        $query = Showcase::approved()
            ->latest('approved_at');

        if ($platform === 'mobile') {
            $query->withMobile();
        } elseif ($platform === 'desktop') {
            $query->withDesktop();
        }

        $showcases = $query->paginate(10);

        return view('showcase', [
            'showcases' => $showcases,
            'platform' => $platform,
        ]);
    }
}
