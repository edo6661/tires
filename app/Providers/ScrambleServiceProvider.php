<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Route;

class ScrambleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Scramble to include all API routes
        Scramble::routes(function (Route $route) {
            // Include all routes that start with 'api/v1/'
            return str_starts_with($route->uri(), 'api/v1/');
        });

        // Add API authentication info and tags
        Scramble::extendOpenApi(function (\Dedoc\Scramble\Support\Generator\OpenApi $openApi) {
            // Add Bearer token authentication
            $openApi->secure(
                \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer', 'sanctum')
            );

            // Add tags for better organization - using direct modification
            // $openApi->tags method doesn't exist, so we'll rely on @tags annotations in controllers
        });
    }
}
