<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get available locales from config
        $locales = array_keys(config('app.available_locales'));

        // Priority: authenticated user's preference -> session -> Accept-Language header -> app default
        $locale = null;

        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
        }

        if (! $locale) {
            $locale = $request->session()->get('locale');
        }

        if (! $locale) {
            $accept = $request->getPreferredLanguage(array_keys(config('app.available_locales')));
            $locale = $accept ?? config('app.locale');
        }

        // Validate and set locale
        if (in_array($locale, $locales)) {
            App::setLocale($locale);
            // persist into session so non-authenticated requests can use it
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
