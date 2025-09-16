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

                // Configure API documentation tags with clear names and detailed descriptions
                // Organized by access level: Public → Customer → Admin with logical sub-grouping
                $openApi->tags = [
                    // ==========================================
                    // PUBLIC API SECTION
                    // No authentication required - open access
                    // ==========================================
                    new Tag('Public', 'Endpoints accessible without authentication - includes menu listings, announcements, and general information'),
                    new Tag('Authentication', 'User login, registration, password reset, and token management endpoints'),

                    // ==========================================
                    // CUSTOMER API SECTION
                    // Requires customer authentication (Sanctum)
                    // Organized by functional areas
                    // ==========================================
                    new Tag('Customer - Dashboard', 'Customer home dashboard with summary statistics, recent activities, and quick access features'),
                    new Tag('Customer - Profile', 'Personal profile management including contact information, preferences, and account settings'),
                    new Tag('Customer - Booking', 'New reservation creation, availability checking, menu selection, and booking confirmation'),
                    new Tag('Customer - Reservation', 'Manage existing reservations - view, modify, cancel, and track reservation history'),
                    new Tag('Customer - TireStorage', 'Tire storage service management - register, track, and manage stored tire inventory'),
                    new Tag('Customer - Contact', 'Customer support communication - submit inquiries, view responses, and support ticket management'),

                    // ==========================================
                    // ADMIN API SECTION
                    // Requires admin authentication and permissions
                    // Comprehensive management and analytics tools
                    // ==========================================

                    // Core Admin Dashboard & Analytics
                    new Tag('Admin - Dashboard', 'Administrative overview dashboard with key metrics, charts, recent activities, and system health monitoring'),

                    // Customer & User Management
                    new Tag('Admin - Customer Management', 'Customer database management with analytics, segmentation (first-time/repeat/dormant), and customer insights'),
                    new Tag('Admin - User Management', 'System user account management including admins and customers, roles, permissions, and authentication control'),
                    new Tag('Admin - Profile Settings', 'Administrator profile management and personal account settings for logged-in admin users'),

                    // Service & Menu Management
                    new Tag('Admin - Menu Management', 'Service menu administration - create, edit, pricing, scheduling, availability, and multilingual content management'),

                    // Booking & Reservation Management
                    new Tag('Admin - Reservation Management', 'Comprehensive booking system administration with calendar views, availability management, and reservation analytics'),
                    new Tag('Admin - Blocked Period Management', 'Time slot blocking system for maintenance, holidays, or special events with conflict detection and calendar integration'),

                    // Storage & Service Management
                    new Tag('Admin - Tire Storage Management', 'Tire storage service administration including inventory tracking, customer storage records, and service lifecycle management'),

                    // Content & Communication Management
                    new Tag('Admin - Announcement Management', 'System-wide announcements and notifications with scheduling, targeting, and multilingual support'),
                    new Tag('Admin - Contact Management', 'Customer inquiry management system with response tracking, categorization, and communication history'),
                    new Tag('Admin - Faq Management', 'FAQ content management with categorization, ordering, and multilingual support for customer self-service'),
                    new Tag('Admin - Questionnaire Management', 'Customer feedback and survey system with response analytics and questionnaire template management'),

                    // System Configuration
                    new Tag('Admin - Business Setting Management', 'Core business configuration including operating hours, company information, and system-wide settings'),
                    new Tag('Admin - Payment Settings', 'Payment system administration including transaction monitoring, payment method configuration, and financial reporting'),
                ];
            });
    }
}
