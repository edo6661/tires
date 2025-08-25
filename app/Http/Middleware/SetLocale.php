<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    protected array $supportedLocales = [
        'en' => [
            'name' => 'English',
            'script' => 'Latn',
            'dir' => 'ltr',
            'flag' => '🇺🇸',
        ],
        'ja' => [
            'name' => '日本語',
            'script' => 'Jpan',
            'dir' => 'ltr',
            'flag' => '🇯🇵',
        ],
    ];

    // Mapping antara prefix route dan locale internal
    protected array $routeToLocaleMap = [
        'en' => 'en',
        'jp' => 'ja',  // jp di route akan dimap ke ja secara internal
    ];

    // Mapping sebaliknya untuk generate URL
    protected array $localeToRouteMap = [
        'en' => 'en',
        'ja' => 'jp',  // ja internal akan dimap ke jp di route
    ];

    public function handle(Request $request, Closure $next)
    {
        $routePrefix = $request->route('locale');
        $locale = $this->routePrefixToLocale($routePrefix);

        if (!$this->isValidLocale($locale)) {
            $locale = $this->getDefaultLocale();
            $routePrefix = $this->localeToRoutePrefix($locale);

            if ($request->route('locale') !== $routePrefix) {
                $newUrl = $this->buildUrlWithLocale($request, $routePrefix);
                return redirect($newUrl, 301);
            }
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $this->localeToRoutePrefix($locale)]);
        Session::put('locale', $locale);

        $this->setCarbonLocale($locale);

        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('Content-Language', $locale);
        }

        return $response;
    }

    protected function routePrefixToLocale(?string $routePrefix): ?string
    {
        return $this->routeToLocaleMap[$routePrefix] ?? null;
    }

    protected function localeToRoutePrefix(string $locale): string
    {
        return $this->localeToRouteMap[$locale] ?? $locale;
    }

    protected function isValidLocale(?string $locale): bool
    {
        return $locale && array_key_exists($locale, $this->supportedLocales);
    }

    protected function getDefaultLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    protected function buildUrlWithLocale(Request $request, string $routePrefix): string
    {
        $segments = $request->segments();
        $segments[0] = $routePrefix;
        return url('/' . implode('/', $segments) .
            ($request->getQueryString() ? '?' . $request->getQueryString() : ''));
    }

    protected function setCarbonLocale(string $locale): void
    {
        $carbonLocales = [
            'en' => 'en_US',
            'ja' => 'ja_JP',
        ];

        if (isset($carbonLocales[$locale])) {
            \Carbon\Carbon::setLocale($carbonLocales[$locale]);
            setlocale(LC_TIME, $carbonLocales[$locale] . '.UTF-8');
        }
    }

    public static function getSupportedLocales(): array
    {
        return (new self())->supportedLocales;
    }

    // Method baru untuk mendapatkan supported route prefixes
    public static function getSupportedRoutePrefixes(): array
    {
        $instance = new self();
        return array_keys($instance->routeToLocaleMap);
    }

    public static function getLocaleInfo(string $locale): ?array
    {
        $locales = self::getSupportedLocales();
        return $locales[$locale] ?? null;
    }

    // Helper method untuk mendapatkan route prefix dari locale
    public static function getRoutePrefix(string $locale): string
    {
        $instance = new self();
        return $instance->localeToRoutePrefix($locale);
    }
}
