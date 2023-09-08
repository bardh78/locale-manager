<?php

namespace Autoluminescent\LocaleManager;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

class LocaleManager
{
    private array|Collection $locales;
    private string $defaultLocale;
    private string $sessionLocaleSwitchMethods;
    private array $localesByKey;

    public function __construct()
    {
        $this->locales = collect(config('locale.locales'));
        $this->defaultLocale = config('locale.default');
        $this->sessionLocaleSwitchMethods = config('locale.session_locale_switch_query_string');

        $this->localesByKey = $this->locales->pluck('key')->toArray();
    }

    public static function all(): array|Collection
    {
        return app()->make(LocaleManager::class)->locales;
    }

    public static function getLocalesByKey(): array
    {
        return app()->make(LocaleManager::class)->localesByKey;
    }

    public static function getDefaultLocale(): string
    {
        return app()->make(LocaleManager::class)->defaultLocale;
    }
    public static function getSessionLocaleSwitchMethods(): string
    {
        return app()->make(LocaleManager::class)->sessionLocaleSwitchMethods;
    }

    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    public static function localeRoute(string $route, $params = []): string
    {
        $defaultLocale = self::getCurrentLocale();

        return route("{$defaultLocale}.$route", $params);
    }

    public static function languageMenu(): array|Collection
    {
        $route = request()->route();
        $currentRouteParameters = $route->parameters();
        $currentRoute = $route->getName();
        $currentRouteNameArr = explode('.', $currentRoute);
        unset($currentRouteNameArr[0]);
        $currentRouteNameWithoutLocale = implode('.', $currentRouteNameArr);

        $currentLocale = app()->getLocale();
        $localeManager = app()->make(LocaleManager::class);
        $defaultLocale = $localeManager->defaultLocale;

        return $localeManager->locales->map(function ($locale) use ($defaultLocale, $currentLocale, $currentRouteParameters, $currentRouteNameWithoutLocale) {
            $locale['current'] = false;
            if ($locale['key'] === $currentLocale) {
                $locale['current'] = true;
            }

            try {
                $locale['href'] = route($locale['key'] . '.' . $currentRouteNameWithoutLocale, $currentRouteParameters);
            } catch (\Exception $exception) {
                $locale['href'] = route($defaultLocale . '.' . $currentRouteNameWithoutLocale, $currentRouteParameters);
            }

            return $locale;
        });
    }

}