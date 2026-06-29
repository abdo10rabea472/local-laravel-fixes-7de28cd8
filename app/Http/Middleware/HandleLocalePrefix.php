<?php

namespace App\Http\Middleware;

use App\Services\LanguageService;
use Closure;
use Illuminate\Http\Request;

/**
 * Global middleware (runs before routing).
 *
 * Policy: URLs DO NOT contain a language prefix.
 * Language is detected by cookie / ?lang= / default (see SetLocale).
 *
 * Backward-compat: if an old URL still has /{locale}/... we:
 *   1. Persist the locale in a cookie.
 *   2. 301-redirect to the same URL WITHOUT the prefix.
 */
class HandleLocalePrefix
{
    public function __construct(protected LanguageService $languages) {}

    public function handle(Request $request, Closure $next)
    {
        $first = $request->segment(1);

        if ($first && $this->languages->exists($first) && $request->isMethod('GET')) {
            $path = '/' . ltrim(substr($request->getPathInfo(), strlen('/' . $first)), '/');
            if ($path === '') $path = '/';
            $qs = $request->getQueryString();
            $target = $path . ($qs ? '?' . $qs : '');

            cookie()->queue(cookie()->forever('locale', $first));

            return redirect($target, 301)
                ->withCookie(cookie()->forever('locale', $first));
        }

        return $next($request);
    }
}
