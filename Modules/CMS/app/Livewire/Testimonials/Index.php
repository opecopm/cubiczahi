<?php

namespace Modules\CMS\Livewire\Testimonials;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Testimonial;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'id';
    public $sortDirection = 'desc';

    protected $listeners = [
        'testimonialDeleted' => '$refresh',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id)
    {
        $testimonial = Testimonial::find($id);
        if ($testimonial) {
            $testimonial->delete();
            session()->flash('message', 'Testimonial deleted successfully.');
        }

        $this->resetPage(); // ensure pagination is correct
    }

    public function render()
    {
        $locale = 'en'; // Show English only in table, but other locales are saved in DB

        $query = Testimonial::query()
            ->when($this->search, function ($query) use ($locale) {
                $query->where(function ($q) use ($locale) {
                    $q->where("name->{$locale}", 'like', '%' . $this->search . '%')
                      ->orWhere("designation->{$locale}", 'like', '%' . $this->search . '%')
                      ->orWhere("company->{$locale}", 'like', '%' . $this->search . '%')
                      ->orWhere("email", 'like', '%' . $this->search . '%');
                });
            });

        // Compute counts based on the search query before applying status filters (similar to Pages)
        $totalCount    = (clone $query)->count();
        $activeCount   = (clone $query)->where('status', 1)->count();
        $inactiveCount = (clone $query)->where('status', 0)->count();

        // Apply status filter if active
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === 'active' ? 1 : 0);
        }

        // Apply dynamic sorting, taking into account Spatie translatable json attributes
        $sortField = $this->sortBy;
        if (in_array($sortField, ['name', 'designation', 'company'])) {
            $sortField = $sortField . '->' . $locale;
        }

        $testimonials = $query->orderBy($sortField, $this->sortDirection)
                             ->paginate(10);

        return view('cms::livewire.testimonials.index', [
            'testimonials' => $testimonials,
            'locale' => $locale,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
        ]);
    }
}
