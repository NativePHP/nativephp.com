<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = Session::get('viewing_docs_lang') ?? $request->getPreferredLanguage(['en', 'es']) ?? 'en';

        app()->setLocale($lang);
        session(['viewing_docs_lang' => $lang]);

        return $next($request);
    }
}
