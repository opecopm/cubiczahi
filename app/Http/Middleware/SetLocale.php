<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Modules\Global\Models\Language;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Cache active languages as a plain array — safe to serialize
        $languages = Cache::remember('active_languages', 3600, function () {
            return Language::where('status', 'active')
                ->get()
                ->map(fn ($lang) => [
                    'code'       => $lang->code,
                    'name'       => $lang->name,
                    'direction'  => $lang->direction,
                    'is_default' => (bool) $lang->is_default,
                ])
                ->keyBy('code')
                ->toArray();
        });

        $defaultEntry = collect($languages)->firstWhere('is_default', true);
        $defaultCode  = $defaultEntry['code'] ?? 'en';

        // ── Priority 1: URL prefix (/ar/shop → locale=ar) ───────────────────
        // Non-default locales appear as the first URL segment.
        // We ALSO write to session so Livewire AJAX requests (POST /livewire/update,
        // which have no /ar/ prefix) still use the correct locale on re-renders.
        $firstSegment = $request->segment(1);

        // Is this a Livewire AJAX request? (no URL prefix, but needs locale from session)
        $isLivewire = $request->hasHeader('X-Livewire')
                   || $request->is('livewire/*');

        if ($firstSegment && isset($languages[$firstSegment]) && $firstSegment !== $defaultCode) {
            // ── URL has an explicit non-default locale prefix ─────────────────
            $code = $firstSegment;
            session(['locale' => $code]);          // ← sync session from URL

        } elseif ($isLivewire && session('locale') && isset($languages[session('locale')])) {
            // ── Livewire AJAX: no URL prefix, use session so re-renders match ─
            $code = session('locale');

        } else {
            // ── Regular page load with no locale prefix = default language ────
            // Always clear the session here so switching back to English works.
            $code = $defaultCode;
            session()->forget('locale');
        }

        App::setLocale($code);

        // Share with all views (layouts, partials, Livewire components, etc.)
        $currentLangData = $languages[$code] ?? [
            'code' => $code, 'name' => $code, 'direction' => 'ltr', 'is_default' => false,
        ];

        view()->share('currentLang',     (object) $currentLangData);
        view()->share('activeLanguages', collect($languages)->map(fn ($l) => (object) $l)->values());

        return $next($request);
    }
}
