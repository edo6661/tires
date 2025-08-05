<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'setLocale' => SetLocale::class,
            'apiSetLocale' => ApiSetLocale::class,
        ]);
        
        $middleware->redirectGuestsTo(function () {
            $locale = app()->getLocale() ?? config('app.fallback_locale', 'en');
            return route('login', ['locale' => $locale]);
        });

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            $locale = $request->route('locale') ?? config('app.fallback_locale', 'en');
            
            if (view()->exists("errors.{$locale}.404")) {
                return response()->view("errors.{$locale}.404", [], 404);
            }
            
            return response()->view('errors.404', ['locale' => $locale], 404);
        });
    })->create();
