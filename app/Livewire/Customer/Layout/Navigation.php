<?php

namespace App\Livewire\Customer\Layout;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Modules\Global\Models\Language;

class Navigation extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        // Load from the same cache the middleware uses — no extra DB hit
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

        $activeLanguages = collect($languages)->map(fn ($l) => (object) $l)->values();
        $currentLocale   = app()->getLocale();

        return view(theme_view('livewire.layout.navigation'), [
            'activeLanguages' => $activeLanguages,
            'currentLocale'   => $currentLocale,
        ]);
    }
}
