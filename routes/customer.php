<?php

use App\Http\Controllers\Customer\AboutController;
use App\Http\Controllers\Customer\FrontendController;
use App\Http\Controllers\Customer\AddressesController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\ContactController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\OrderConfirmController;
use App\Http\Controllers\Customer\OrdersController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\SecurityController;
use App\Http\Controllers\Customer\LegalController;
use App\Livewire\Actions\Logout;
use App\Http\Controllers\Customer\ShopController;
use App\Livewire\Customer\Auth\Login;
use App\Livewire\Customer\Auth\MfaChallenge;
use App\Livewire\Customer\Auth\Register;
use App\Livewire\Customer\OrderBuilder;
use Illuminate\Support\Facades\Route;

// Returns a closure so web.php can register these routes for every locale
return static function (): void {

    // Public
    
    Route::get('/', [FrontendController::class, 'show'])->defaults('slug', 'home')->name('home');
    Route::get('/about',     [FrontendController::class, 'show'])->defaults('slug', 'about')->name('about');
    Route::get('/contact',   [FrontendController::class, 'show'])->defaults('slug', 'contact')->name('contact');
    Route::get('/shop',      [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/deals',     [CatalogController::class, 'deals'])->name('catalog.deals');
    Route::get('/shop/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
    Route::get('/cart',      [ShopController::class,    'index'])->name('cart.index');

    // Legal
    Route::get('/privacy-policy',       [FrontendController::class, 'show'])->defaults('slug', 'privacy-policy')->name('legal.privacy');
    Route::get('/terms-and-conditions', [FrontendController::class, 'show'])->defaults('slug', 'terms-and-conditions')->name('legal.terms');
    Route::get('/refund-policy',        [FrontendController::class, 'show'])->defaults('slug', 'refund-policy')->name('legal.refund');

    // Guest only
    Route::middleware('guest')->name('customer.')->group(function () {
        Route::get('/login',         Login::class)->name('login');
        Route::get('/register',      Register::class)->name('register');
        Route::get('/mfa/challenge', MfaChallenge::class)->name('mfa.challenge');
    });

    // Auth required — customer only
    Route::middleware(['auth', 'user_type:customer'])->name('customer.')->group(function () {
        Route::get('/dashboard',               [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/order',                   OrderBuilder::class)->name('order.builder');
        Route::get('/checkout',                [ShopController::class,     'checkout'])->name('checkout.index');
        Route::get('/order/confirm/{orderId}', [OrderConfirmController::class, 'show'])
            ->middleware(['auth'])->name('order.confirm');

        Route::get('/profile',              [ProfileController::class, 'index'])->name('profile');
        Route::get('/security',             [SecurityController::class,'index'])->name('security');
        Route::get('/orders',               [OrdersController::class,  'index'])->name('orders.index');
        Route::get('/orders/{orderId}',     [OrdersController::class,  'show'])->name('orders.show');
        Route::get('/addresses',            [AddressesController::class,'index'])->name('addresses.index');

        Route::post('/logout', function (Logout $logout) {
            $logout();
            // lroute() reads the current locale — sends Arabic users back to /ar
            return redirect(lroute('home'));
        })->name('logout');
    });

    // Handle CMS dynamic pages as catch-all fallback route, excluding system/admin and localized ar prefixes
    Route::get('{slug}', [FrontendController::class, 'show'])
        ->where('slug', '^(?!admin|api|locale|media-content|assets|ar$|ar/).*')
        ->name('cms.page');
};
