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
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');
        if (!$this->isValidLocale($locale)) {
            $locale = $this->getDefaultLocale();
            if ($request->route('locale') !== $locale) {
                $newUrl = $this->buildUrlWithLocale($request, $locale);
                return redirect($newUrl, 301);
            }
        }
        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        Session::put('locale', $locale);
        $this->setCarbonLocale($locale);
        $response = $next($request);
        if (method_exists($response, 'header')) {
            $response->header('Content-Language', $locale);
        }
        return $response;
    }
    protected function isValidLocale(?string $locale): bool
    {
        return $locale && array_key_exists($locale, $this->supportedLocales);
    }
    protected function getDefaultLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }
    protected function buildUrlWithLocale(Request $request, string $locale): string
    {
        $segments = $request->segments();
        $segments[0] = $locale; 
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
    public static function getLocaleInfo(string $locale): ?array
    {
        $locales = self::getSupportedLocales();
        return $locales[$locale] ?? null;
    }
}