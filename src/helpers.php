<?php

if (! function_exists('locale_route')) {
    function locale_route($route, $params = [])
    {
        return \Autoluminescent\LocaleManager\LocaleManager::localeRoute($route, $params);
    }
}

if (! function_exists('translate')) {
    function translate($key, $isAttribute = false)
    {
        if ($isAttribute){
            return strtolower(__($key));
        }

        return __($key);
    }
}
