<?php

namespace App\Providers;

use App\Services\BusinessSettingService;
use App\Services\SystemSettingService;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade; // ✅ Add this line
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Services\CartService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
// use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Modules\IAM\Models\Permission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission as SpatiePermission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ✅ Register singletons for both setting services
        $this->app->singleton(SystemSettingService::class, function () {
            return new SystemSettingService;
        });

        $this->app->singleton(BusinessSettingService::class, function () {
            return new BusinessSettingService;
        });
    }

    public function boot(): void
    {
        // ✅ Map Spatie Permission model to custom Permission model
        $this->app->bind(SpatiePermission::class, Permission::class);

        Gate::before(function ($user, $ability) {
            if (! $user) {
                return null;
            }

            if ($user->hasRole('Admin')) {
                return true;
            }

            try {
                if ($user->hasPermissionTo($ability)) {
                    return true;
                }
            } catch (PermissionDoesNotExist $e) {
            }

            return null;
        });

        /**
         * ---------------------------------------------------------
         * Passport Token Expiry Configuration
         * ---------------------------------------------------------
         */
        // Passport::tokensExpireIn(CarbonInterval::days(15));
        // Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        // Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        // ✅ Share dynamic menu with all authenticated views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $menu = Auth::user()->menu;
                $view->with('user_menu', $menu);
            }
        });

        // Register active theme components as <x-theme.*>
        Blade::anonymousComponentPath(
            resource_path('views/themes/' . config('theme.active') . '/components'),
            'theme'
        );

        // Merge session cart into DB cart upon login
        Event::listen(Login::class, function (Login $event) {
            app(CartService::class)->mergeSessionCartToDb();
        });
    }
}
