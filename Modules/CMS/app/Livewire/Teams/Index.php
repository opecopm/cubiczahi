<?php

namespace Modules\CMS\Livewire\Teams;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Team;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search = '';

    protected $listeners = [
        'teamUpdated' => '$refresh',
        'teamCreated' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        session()->flash('success', 'Team member deleted successfully!');
    }

    public function render()
    {
        $query = Team::query();

        if ($this->search) {
            $locales = ['en', 'ur', 'ar']; // all locales
            $fields = ['name', 'designation', 'phone'];

            $query->where(function ($q) use ($fields, $locales) {
                foreach ($fields as $field) {
                    foreach ($locales as $locale) {
                        $q->orWhereTranslationLike($field, $locale, '%' . $this->search . '%');
                    }
                }
            });
        }

        $teams = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('cms::livewire.teams.index', [
            'teams' => $teams,
        ]);
    }
}
