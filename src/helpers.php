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
            return strtolower(__('webapp.registration.regular.last_name'));
        }

        return __('webapp.registration.regular.last_name');
    }
}