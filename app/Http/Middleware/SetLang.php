<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Symfony\Component\HttpFoundation\Response;

class SetLang
{
    public function handle(Request $request, Closure $next): Response
    {
        return app(StartSession::class)
            ->handle($request, function ($request) use ($next) {
                if (!session()->has('current_lang')) {
                    session()->put('current_lang', 'ro');
                }

                app()->setLocale(session('current_lang', 'ro'));

                return $next($request);
            });
    }
}
