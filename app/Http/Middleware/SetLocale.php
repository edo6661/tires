<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Supported locales configuration
     */
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

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');
        
        // Validasi locale
        if (!$this->isValidLocale($locale)) {
            $locale = $this->getDefaultLocale();
            
            // Redirect ke URL dengan locale yang valid jika URL tidak valid
            if ($request->route('locale') !== $locale) {
                $newUrl = $this->buildUrlWithLocale($request, $locale);
                return redirect($newUrl, 301);
            }
        }

        // Set application locale
        App::setLocale($locale);
        
        // Set URL defaults untuk helper route()
        URL::defaults(['locale' => $locale]);

        // Store locale di session untuk reference
        Session::put('locale', $locale);

        // Set locale untuk Carbon dates
        $this->setCarbonLocale($locale);

        // Set response headers untuk SEO
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('Content-Language', $locale);
        }

        return $response;
    }

    /**
     * Check if locale is valid
     */
    protected function isValidLocale(?string $locale): bool
    {
        return $locale && array_key_exists($locale, $this->supportedLocales);
    }

    /**
     * Get default locale
     */
    protected function getDefaultLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    /**
     * Build URL with correct locale
     */
    protected function buildUrlWithLocale(Request $request, string $locale): string
    {
        $segments = $request->segments();
        $segments[0] = $locale; // Replace first segment with correct locale
        
        return url('/' . implode('/', $segments) . 
                  ($request->getQueryString() ? '?' . $request->getQueryString() : ''));
    }

    /**
     * Set Carbon locale
     */
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

    /**
     * Get supported locales
     */
    public static function getSupportedLocales(): array
    {
        return (new self())->supportedLocales;
    }

    /**
     * Get locale info
     */
    public static function getLocaleInfo(string $locale): ?array
    {
        $locales = self::getSupportedLocales();
        return $locales[$locale] ?? null;
    }
}