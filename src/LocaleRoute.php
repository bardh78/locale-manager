<?php

namespace Autoluminescent\LocaleManager;

use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use RuntimeException;
use Closure;


/**
 * Class LocaleRoute
 * @method LocaleRoute af(string $uri) Afrikaans
 * @method LocaleRoute ar(string $uri) Arabic
 * @method LocaleRoute az(string $uri) Azerbaijani
 * @method LocaleRoute be(string $uri) Belarusian
 * @method LocaleRoute bg(string $uri) Bulgarian
 * @method LocaleRoute bn(string $uri) Bengali
 * @method LocaleRoute bs(string $uri) Bosnian
 * @method LocaleRoute ca(string $uri) Catalan
 * @method LocaleRoute cs(string $uri) Czech
 * @method LocaleRoute cy(string $uri) Welsh
 * @method LocaleRoute da(string $uri) Danish
 * @method LocaleRoute de(string $uri) German
 * @method LocaleRoute el(string $uri) Greek
 * @method LocaleRoute en(string $uri) English
 * @method LocaleRoute eo(string $uri) Esperanto
 * @method LocaleRoute es(string $uri) Spanish
 * @method LocaleRoute et(string $uri) Estonian
 * @method LocaleRoute eu(string $uri) Basque
 * @method LocaleRoute fa(string $uri) Persian
 * @method LocaleRoute fi(string $uri) Finnish
 * @method LocaleRoute fr(string $uri) French
 * @method LocaleRoute ga(string $uri) Irish
 * @method LocaleRoute gd(string $uri) Scottish Gaelic
 * @method LocaleRoute gl(string $uri) Galician
 * @method LocaleRoute gu(string $uri) Gujarati
 * @method LocaleRoute he(string $uri) Hebrew
 * @method LocaleRoute hi(string $uri) Hindi
 * @method LocaleRoute hr(string $uri) Croatian
 * @method LocaleRoute hu(string $uri) Hungarian
 * @method LocaleRoute hy(string $uri) Armenian
 * @method LocaleRoute id(string $uri) Indonesian
 * @method LocaleRoute is(string $uri) Icelandic
 * @method LocaleRoute it(string $uri) Italian
 * @method LocaleRoute ja(string $uri) Japanese
 * @method LocaleRoute jv(string $uri) Javanese
 * @method LocaleRoute ka(string $uri) Georgian
 * @method LocaleRoute kk(string $uri) Kazakh
 * @method LocaleRoute km(string $uri) Khmer
 * @method LocaleRoute kn(string $uri) Kannada
 * @method LocaleRoute ko(string $uri) Korean
 * @method LocaleRoute ky(string $uri) Kyrgyz
 * @method LocaleRoute la(string $uri) Latin
 * @method LocaleRoute lt(string $uri) Lithuanian
 * @method LocaleRoute lv(string $uri) Latvian
 * @method LocaleRoute mk(string $uri) Macedonian
 * @method LocaleRoute ml(string $uri) Malayalam
 * @method LocaleRoute mn(string $uri) Mongolian
 * @method LocaleRoute mr(string $uri) Marathi
 * @method LocaleRoute ms(string $uri) Malay
 * @method LocaleRoute mt(string $uri) Maltese
 * @method LocaleRoute my(string $uri) Burmese
 * @method LocaleRoute nb(string $uri) Norwegian Bokmål
 * @method LocaleRoute ne(string $uri) Nepali
 * @method LocaleRoute nl(string $uri) Dutch
 * @method LocaleRoute nn(string $uri) Norwegian Nynorsk
 * @method LocaleRoute no(string $uri) Norwegian
 * @method LocaleRoute pa(string $uri) Punjabi
 * @method LocaleRoute pl(string $uri) Polish
 * @method LocaleRoute ps(string $uri) Pashto
 * @method LocaleRoute pt(string $uri) Portuguese
 * @method LocaleRoute ro(string $uri) Romanian
 * @method LocaleRoute ru(string $uri) Russian
 * @method LocaleRoute sd(string $uri) Sindhi
 * @method LocaleRoute si(string $uri) Sinhala
 * @method LocaleRoute sk(string $uri) Slovak
 * @method LocaleRoute sl(string $uri) Slovenian
 * @method LocaleRoute sq(string $uri) Albanian
 * @method LocaleRoute sr(string $uri) Serbian
 * @method LocaleRoute sv(string $uri) Swedish
 * @method LocaleRoute sw(string $uri) Swahili
 * @method LocaleRoute ta(string $uri) Tamil
 * @method LocaleRoute te(string $uri) Telugu
 * @method LocaleRoute th(string $uri) Thai
 * @method LocaleRoute tl(string $uri) Tagalog
 * @method LocaleRoute tr(string $uri) Turkish
 * @method LocaleRoute uk(string $uri) Ukrainian
 * @method LocaleRoute ur(string $uri) Urdu
 * @method LocaleRoute vi(string $uri) Vietnamese
 * @method LocaleRoute xh(string $uri) Xhosa
 * @method LocaleRoute zh(string $uri) Chinese
 * @method LocaleRoute zu(string $uri) Zulu
 */
class LocaleRoute
{
    private string $controller;
    private string $method;
    private string $action;
    private string $defaultLocale;
    private array $routes = [];

    protected static array $groupStack = [];
    protected array $middleware = ['locale'];

    public function __construct($uri, $method, $controller, $action)
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->method = $method;
        $this->defaultLocale = LocaleManager::getDefaultLocale();

        if (is_array($uri)) {
            $this->addRoutesFromArray($uri);
        } else {
            $this->route($this->method, $uri);
        }
    }

    public static function session(string $name, Closure $callback): void
    {
        Route::middleware(["locale:{$name}"])->group(function () use ($callback) {
            $callback();
        });
    }

    public static function middleware(string|array $middleware, Closure $callback): void
    {
        static::$groupStack[] = $middleware;

        $callback();

       // array_pop(static::$groupStack);
    }

    public static function get($defaultLocaleUri, array $controller): LocaleRoute
    {
        return self::actionMethod('GET', $defaultLocaleUri, $controller);
    }

    public static function post($defaultLocaleUri, array $controller): LocaleRoute
    {
        return self::actionMethod('POST', $defaultLocaleUri, $controller);
    }

    public static function actionMethod(string $method, $defaultLocaleUri, array $controller): LocaleRoute
    {
        $instance = new self($defaultLocaleUri, $method, $controller[0], $controller[1]);

        if (count(static::$groupStack) > 0) {
            $currentGroupAttributes = end(static::$groupStack);
            if (isset($currentGroupAttributes)) {
                if (is_array($currentGroupAttributes)) {
                    $instance->middleware = [...$instance->middleware, ...$currentGroupAttributes];
                } else {
                    $instance->middleware[] = $currentGroupAttributes;
                }
            }
        }

        return $instance;
    }

    public function name($name): void
    {
        foreach ($this->routes as $route) {
            $routePrefix = $route['prefix'];
            $routeName = $routePrefix ? "{$routePrefix}.{$name}" : "{$this->defaultLocale}.{$name}";

            Route::controller($this->controller)->group(function () use ($routePrefix, $routeName, $route) {
                $routeDefinition = Route::prefix($routePrefix)
                    ->get($route['uri'], [$this->controller, $this->action])
                    ->name($routeName);
                // Apply middleware from group attributes, if any
                if (!empty($this->middleware)) {
                    $routeDefinition->middleware($this->middleware);
                }
            });
        }
    }

    public function __call(string $language, array $arguments)
    {
        $uri = $arguments[0] ?? '';

        if (!is_string($uri)) {
            throw new InvalidArgumentException("The URI must be a string.");
        }

        $localesByKey = LocaleManager::getLocalesByKey();

        if (empty($localesByKey)) {
            throw new RuntimeException("No locales have been defined in config.locale.");
        }

        if (in_array($language, $localesByKey)) {
            $this->route($this->method, $uri, $language);
        } else {
            throw new InvalidArgumentException("Language '{$language}' is not defined in config.locale");
        }

        return $this;
    }

    private function route(string $method, string $uri, string $prefix = ''): void
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'prefix' => $prefix,
        ];
    }

    private function addRoutesFromArray(array $uris): void
    {
        foreach ($uris as $prefix => $uri) {
            $this->route($this->method, $uri, $prefix);
        }
    }
}
