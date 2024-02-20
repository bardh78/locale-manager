<?php

namespace Autoluminescent\LocaleManager\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class LocaleRoute
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
