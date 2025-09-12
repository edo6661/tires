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

                    // Customer section with sub-groups
                    new Tag('Customer - Dashboard', 'Customer dashboard and summary endpoints'),
                    new Tag('Customer - Profile', 'Customer profile management endpoints'),
                    new Tag('Customer - Booking', 'Customer booking and reservation endpoints'),
                    new Tag('Customer - Reservation', 'Customer reservation endpoints'),
                    new Tag('Customer - TireStorage', 'Customer Tire storage endpoints'),
                    new Tag('Customer - Contact', 'Customer support and inquiry endpoints'),


                    // Admin section with sub-groups
                    new Tag('Admin - Dashboard', 'Administrative dashboard and statistics'),
                    new Tag('Admin - Customer Management', 'Administrative customer management endpoints'),
                    new Tag('Admin - Menu Management', 'Administrative menu management endpoints'),
                    new Tag('Admin - Reservation Management', 'Administrative booking and reservation management'),
                    new Tag('Admin - Tire Storage Management', 'Administrative tire storage endpoints'),
                    new Tag('Admin - Announcement Management', 'Administrative menu management endpoints'),
                    new Tag('Admin - Questionnaire Management', 'Administrative questionnaire management endpoints'),
                    new Tag('Admin - Contact Management', 'Administrative contact management endpoints'),
                    new Tag('Admin - Business Setting Management', 'Administrative business setting management endpoints'),
                    new Tag('Admin - Blocked Period Management', 'Administrative blocked period menu management endpoints'),
                    new Tag('Admin - FAQ Management', 'Administrative faq management'),
                ];
            });
    }
}
