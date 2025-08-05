<?php
// app/Http/Middleware/ApiSetLocale.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ApiSetLocale
{
    protected array $supportedLocales = ['en', 'ja'];
    protected string $defaultLocale = 'en';

    public function handle(Request $request, Closure $next)
    {
        $locale = $this->getLocaleFromRequest($request);
        
        if (!$this->isValidLocale($locale)) {
            $locale = $this->defaultLocale;
        }

        App::setLocale($locale);
        
        $response = $next($request);
        
        // Add locale info to response headers
        if (method_exists($response, 'header')) {
            $response->header('Content-Language', $locale);
            $response->header('X-Locale', $locale);
        }

        return $response;
    }

    protected function getLocaleFromRequest(Request $request): ?string
    {
        // Priority: 1. Accept-Language header, 2. locale query parameter, 3. X-Locale header
        $locale = $request->header('Accept-Language');
        
        if ($locale) {
            // Parse Accept-Language header (e.g., "en-US,en;q=0.9,ja;q=0.8")
            $locale = explode(',', $locale)[0];
            $locale = explode('-', $locale)[0]; // Get only language code
            $locale = strtolower(trim($locale));
        }
        
        // Override with query parameter if present
        if ($request->has('locale')) {
            $locale = $request->get('locale');
        }
        
        // Override with X-Locale header if present
        if ($request->header('X-Locale')) {
            $locale = $request->header('X-Locale');
        }

        return $locale;
    }

    protected function isValidLocale(?string $locale): bool
    {
        return $locale && in_array($locale, $this->supportedLocales);
    }

    public static function getSupportedLocales(): array
    {
        return (new self())->supportedLocales;
    }
}
