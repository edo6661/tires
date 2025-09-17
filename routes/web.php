<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;


Route::fallback(function () {

    if (request()->is('api/*')) {
        return;
    }

    
    $path = request()->path();
    $defaultLocale = config('app.fallback_locale', 'en');
    $defaultRoutePrefix = SetLocale::getRoutePrefix($defaultLocale);
    $supportedPrefixes = SetLocale::getSupportedRoutePrefixes();

    if (in_array($path, $supportedPrefixes)) {
        return redirect('/' . $path . '/', 301);
    }

    if (preg_match('/^(' . implode('|', $supportedPrefixes) . ')\//', $path)) {
        abort(404);
    }

    return redirect('/' . $defaultRoutePrefix . '/' . $path, 301);
});

Route::get('/', function () {
    $defaultLocale = config('app.fallback_locale', 'en');
    $userLocale = request()->getPreferredLanguage(['en', 'ja']) ?? $defaultLocale;

    // Convert locale to route prefix
    $routePrefix = SetLocale::getRoutePrefix($userLocale);

    return redirect()->route('home', ['locale' => $routePrefix]);
});

Route::prefix('{locale}')
    ->whereIn('locale', SetLocale::getSupportedRoutePrefixes()) // Menggunakan method baru
    ->middleware(['setLocale'])
    ->group(function () {

        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/about', [HomeController::class, 'about'])->name('about');
        Route::get('/inquiry', [HomeController::class, 'inquiry'])->name('inquiry');
        Route::post('/inquiry', [HomeController::class, 'submitInquiry'])->name('inquiry.submit');
        Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
        Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
        Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
        Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/menus', [HomeController::class, 'getMenusApi'])->name('menus');
            Route::get('/menus/{menu}', [HomeController::class, 'getMenuApi'])->name('menu');
        });

        require __DIR__ . '/auth.php';
        require __DIR__ . '/customer/booking.php';

        Route::middleware(['auth'])->group(function () {
            require __DIR__ . '/profile.php';

            Route::name('customer.')->group(function () {
                Route::get('/dashboard', function () {
                    return view('customer.dashboard');
                })->name('dashboard');

                require __DIR__ . '/customer/reservation.php';
            });

            Route::middleware('admin')->group(function () {
                Route::name('admin.')->prefix('admin')->group(function () {
                    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

                    require __DIR__ . '/admin/announcement.php';
                    require __DIR__ . '/admin/blocked-period.php';
                    require __DIR__ . '/admin/business-setting.php';
                    require __DIR__ . '/admin/contact.php';
                    require __DIR__ . '/admin/customer.php';
                    require __DIR__ . '/admin/faq.php';
                    require __DIR__ . '/admin/menu.php';
                    require __DIR__ . '/admin/payment.php';
                    require __DIR__ . '/admin/questionnaire.php';
                    require __DIR__ . '/admin/reservation.php';
                    require __DIR__ . '/admin/tire-storage.php';
                    require __DIR__ . '/admin/user.php';
                });
            });
        });
    });

Route::fallback(function () {
    $path = request()->path();
    $defaultLocale = config('app.fallback_locale', 'en');
    $defaultRoutePrefix = SetLocale::getRoutePrefix($defaultLocale);
    $supportedPrefixes = SetLocale::getSupportedRoutePrefixes();

    // Check if path is just a supported locale prefix (e.g., 'jp' or 'en')
    if (in_array($path, $supportedPrefixes)) {
        return redirect('/' . $path . '/', 301);
    }

    // Check if path starts with supported locale prefix followed by slash
    if (preg_match('/^(' . implode('|', $supportedPrefixes) . ')\//', $path)) {
        // Path already has valid locale prefix, this is a real 404
        abort(404);
    }

    // Path doesn't have locale prefix, add default one
    return redirect('/' . $defaultRoutePrefix . '/' . $path, 301);
});
