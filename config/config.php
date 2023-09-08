<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'default' => 'fr',

    'locales' => [
        'fr' => [
            'key' => 'fr',
            'prefix' => 'fr',
            'name' => 'Français',
            'abbreviation' => 'FR',
        ],

        'de' => [
            'key' => 'de',
            'prefix' => 'de',
            'name' => 'German',
            'abbreviation' => 'DE',
        ],

        'it' => [
            'key' => 'it',
            'prefix' => 'it',
            'name' => 'Italiano',
            'abbreviation' => 'IT',
        ],

        'en' => [
            'key' => 'en',
            'prefix' => 'en',
            'name' => 'English',
            'abbreviation' => 'EN',
        ],
    ],

    'session_locale_switch_query_string' => 'lang',

];
