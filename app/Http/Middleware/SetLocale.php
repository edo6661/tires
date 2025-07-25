<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');
        $supportedLocales = ['en', 'ja'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.fallback_locale');
        }
        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        return $next($request);
    }
}