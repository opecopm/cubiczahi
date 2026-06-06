<?php

use App\Services\BusinessSettingService;
use App\Services\SystemSettingService;

if (! function_exists('system_setting')) {
    /**
     * Get a system-level setting value.
     */
    function system_setting(string $key, $default = null)
    {
        return app(SystemSettingService::class)->get($key, $default);
    }
}

if (! function_exists('business_setting')) {
    /**
     * Get a business-level setting value.
     */
    function business_setting(string $key, $default = null)
    {
        return app(BusinessSettingService::class)->get($key, $default);
    }
}

if (! function_exists('web_setting')) {
    /**
     * Get a website-level CMS setting value.
     */
    function web_setting(string $key, $default = null)
    {
        $setting = \Modules\CMS\Models\WebSetting::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return $setting->value ?: $default;
    }
}

if (! function_exists('format_hours_to_hhmmss')) {
    function format_hours_to_hhmmss($hours)
    {
        $hours = (float) $hours;
        $h = floor($hours);
        $m = floor(($hours - $h) * 60);
        $s = round((($hours - $h) * 60 - $m) * 60);

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}

if (! function_exists('format_minutes_to_hhmmss')) {
    function format_minutes_to_hhmmss($minutes)
    {
        $minutes = (float) $minutes;
        $h = floor($minutes / 60);
        $m = floor($minutes % 60);
        $s = round(($minutes - floor($minutes)) * 60);

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}

if (! function_exists('theme_view')) {
    function theme_view(string $path): string
    {
        return 'themes.' . config('theme.active') . '.' . $path;
    }
}

if (! function_exists('theme_asset')) {
    function theme_asset(string $path): string
    {
        return asset('themes/' . config('theme.active') . '/' . $path);
    }
}

/**
 * Locale-aware route() helper.
 *
 * Works exactly like route() but automatically prepends the current locale
 * prefix to the route name for non-default locales.
 *
 * Examples (locale = 'ar'):
 *   lroute('home')           → route('ar.home')           → /ar
 *   lroute('catalog.index')  → route('ar.catalog.index')  → /ar/shop
 *   lroute('home', [], true) → absolute URL
 */
if (! function_exists('lroute')) {
    function lroute(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $locale = app()->getLocale();

        // Always try the locale-prefixed route first (e.g. 'ar.home').
        // This works regardless of what config('app.locale') is set to.
        if ($locale) {
            $localeName = $locale . '.' . $name;
            if (\Illuminate\Support\Facades\Route::has($localeName)) {
                return route($localeName, $parameters, $absolute);
            }
        }

        // Fall back to the unprefixed route (default locale or admin routes).
        return route($name, $parameters, $absolute);
    }
}

/**
 * Returns the equivalent URL of the current page in a target locale.
 *
 * Used by the language switcher so clicking "العربية" from /shop
 * sends the user to /ar/shop, not just /ar.
 */
if (! function_exists('locale_url')) {
    function locale_url(string $targetLocale): string
    {
        $currentLocale = app()->getLocale();
        $path          = request()->path(); // e.g. "ar/shop" or "shop"

        // Strip the current locale prefix by checking if the first path
        // segment matches a known locale prefix (i.e. a named route group exists).
        // We strip whenever 'ar.home' (or similar) exists — meaning it's a routed locale.
        $firstSegment = explode('/', ltrim($path, '/'))[0] ?? '';
        if ($firstSegment && \Illuminate\Support\Facades\Route::has($firstSegment . '.home')) {
            $path = ltrim(substr($path, strlen($firstSegment)), '/');
        }

        // Prepend the target locale prefix only when locale-prefixed routes exist.
        if ($targetLocale && \Illuminate\Support\Facades\Route::has($targetLocale . '.home')) {
            $newPath = $targetLocale . '/' . $path;
        } else {
            $newPath = $path;
        }

        return url($newPath ?: '/');
    }
}

if (! function_exists('media_url')) {
    /**
     * Resolve any image path (local storage upload or Media Gallery) to its correct public URL.
     */
    function media_url($path)
    {
        if (!$path) {
            return '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'media-content/')) {
            return asset($path);
        }
        return asset('storage/' . $path);
    }
}
