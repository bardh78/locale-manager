<?php

namespace Autoluminescent\LocaleManager;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    private string $sessionKey = '';

    public function handle($request, Closure $next, $sessionName = null)
    {
        $locale = LocaleManager::getDefaultLocale();
        $locales = LocaleManager::getLocalesByKey();

        if ($sessionName) {
            $this->sessionKey = "locale.session.{$sessionName}";
            $locale = $this->getLocaleFromSession($sessionName);
        } else {
            if (in_array($request->segment(1), $locales)) {
                $locale = $request->segment(1);
            }
        }


        debug($locale);
        App::setLocale($locale);

        return $next($request);
    }

    private function getLocaleFromSession(mixed $sessionName)
    {
        $localeInputFromQueryString = request()->input('language', LocaleManager::getDefaultLocale());

        $userLocale = auth()->user()->locale ?? null;
        if ($userLocale) {
            return $userLocale;
        }

        if (request()->has('language')) {
            request()->session()->put($this->sessionKey, $localeInputFromQueryString);

            return $localeInputFromQueryString;
        }

        return request()->session()->get($this->sessionKey, $localeInputFromQueryString);
    }
}
