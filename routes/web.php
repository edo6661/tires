<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $defaultLocale = config('app.fallback_locale', 'en');
    $userLocale = request()->getPreferredLanguage(['en', 'ja']) ?? $defaultLocale;
    
    return redirect()->route('home', ['locale' => $userLocale]);
});

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(SetLocale::getSupportedLocales()))
    ->middleware(['setLocale'])
    ->group(function () {
        
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/inquiry', [HomeController::class, 'inquiry'])->name('inquiry');
        Route::post('/inquiry', [HomeController::class, 'submitInquiry'])->name('inquiry.submit');

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
    
    if (!preg_match('/^(en|ja)\//', $path)) {
        return redirect('/' . $defaultLocale . '/' . $path, 301);
    }
    
    abort(404);
});