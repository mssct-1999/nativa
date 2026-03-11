<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale');
        $supported = ['en', 'fr', 'pt'];

        if (is_string($locale) && in_array($locale, $supported, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
