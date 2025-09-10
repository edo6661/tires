<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\Tag;
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
        // Configure Scramble to include all API routes and add authentication/tags
        Scramble::configure()
            ->routes(function (Route $route) {
                // Include all routes that start with 'api/v1/'
                return str_starts_with($route->uri(), 'api/v1/');
            })
            ->withDocumentTransformers(function (\Dedoc\Scramble\Support\Generator\OpenApi $openApi) {
                // Add Bearer token authentication
                $openApi->secure(
                    \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer', 'sanctum')
                );

                // Add tags for better organization
                $openApi->tags = [
                    new Tag('Public', 'Public endpoints that do not require authentication'),
                    new Tag('Authentication', 'User authentication and authorization endpoints'),
                    new Tag('Customer', 'Customer-specific endpoints (requires user authentication)'),
                    new Tag('Admin', 'Administrative endpoints (requires admin authentication)')
                ];
            });
    }
}
