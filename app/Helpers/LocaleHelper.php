<?php

namespace App\Helpers;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocaleHelper
{
    /**
     * Get current locale
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get fallback locale
     */
    public static function getFallbackLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    /**
     * Get all supported locales
     */
    public static function getSupportedLocales(): array
    {
        return SetLocale::getSupportedLocales();
    }

    /**
     * Get locale info
     */
    public static function getLocaleInfo(string $locale): ?array
    {
        return SetLocale::getLocaleInfo($locale);
    }

    /**
     * Check if locale is supported
     */
    public static function isSupported(string $locale): bool
    {
        return array_key_exists($locale, self::getSupportedLocales());
    }

    /**
     * Get locale name for display
     */
    public static function getLocaleName(string $locale): string
    {
        $info = self::getLocaleInfo($locale);
        return $info['name'] ?? $locale;
    }

    /**
     * Get locale flag
     */
    public static function getLocaleFlag(string $locale): string
    {
        $info = self::getLocaleInfo($locale);
        return $info['flag'] ?? '🌐';
    }

    /**
     * Get text direction for locale
     */
    public static function getLocaleDirection(string $locale): string
    {
        $info = self::getLocaleInfo($locale);
        return $info['dir'] ?? 'ltr';
    }

    /**
     * Get route with locale
     */
    public static function route(string $name, array $parameters = [], string $locale = null): string
    {
        $locale = $locale ?: self::getCurrentLocale();
        $parameters['locale'] = $locale;
        
        return route($name, $parameters);
    }

    /**
     * Get current URL with different locale
     */
    public static function getUrlWithLocale(string $targetLocale): string
    {
        $currentRoute = request()->route();
        
        if (!$currentRoute) {
            return url('/' . $targetLocale);
        }

        $parameters = $currentRoute->parameters();
        $parameters['locale'] = $targetLocale;
        
        try {
            return route($currentRoute->getName(), $parameters);
        } catch (\Exception $e) {
            // Fallback jika route name tidak ditemukan
            return url('/' . $targetLocale);
        }
    }

    /**
     * Get all alternative locales with URLs untuk language switcher
     */
    public static function getAlternativeLocales(): array
    {
        $alternatives = [];
        $currentLocale = self::getCurrentLocale();
        
        foreach (self::getSupportedLocales() as $locale => $info) {
            if ($locale !== $currentLocale) {
                $alternatives[] = [
                    'code' => $locale,
                    'name' => $info['name'],
                    'flag' => $info['flag'],
                    'url' => self::getUrlWithLocale($locale),
                    'active' => false,
                ];
            }
        }
        
        return $alternatives;
    }

    /**
     * Get current locale info for display
     */
    public static function getCurrentLocaleInfo(): array
    {
        $locale = self::getCurrentLocale();
        $info = self::getLocaleInfo($locale);
        
        return array_merge($info ?? [], [
            'code' => $locale,
            'active' => true,
        ]);
    }

    /**
     * Get all locales (current + alternatives) untuk complete language switcher
     */
    public static function getAllLocalesWithUrls(): array
    {
        $current = self::getCurrentLocaleInfo();
        $current['url'] = request()->url();
        
        $alternatives = self::getAlternativeLocales();
        
        return array_merge([$current], $alternatives);
    }

    /**
     * Check apakah current locale adalah RTL
     */
    public static function isRtl(): bool
    {
        return self::getLocaleDirection(self::getCurrentLocale()) === 'rtl';
    }

    /**
     * Get HTML lang attribute
     */
    public static function getHtmlLang(): string
    {
        return str_replace('_', '-', self::getCurrentLocale());
    }
}