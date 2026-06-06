<?php

namespace Modules\Business\Livewire\Locations;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\Company;
use Modules\Business\Models\Location;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage = 10;

    public string $search = '';

    public $location = [];

    public $updateMode = false;

    public $deleteId;

    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    protected function rules()
    {
        return [
            'location.company_id' => 'nullable|exists:companies,id',
            'location.name' => 'required|string|max:255',
            'location.code' => 'nullable|string|max:255',
            'location.parent_id' => 'nullable|exists:locations,id',
            'location.type' => 'required|in:'.implode(',', array_keys(Location::TYPE_SELECT)),
            'location.description' => 'nullable|string',
            'location.status' => 'required|in:'.implode(',', array_keys(Location::STATUS_SELECT)),
            'location.is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
        $this->orderable = ['id', 'name', 'code', 'type', 'status'];
        $this->model = new Location;
        $this->initFilters($this->model);
    }

    public function addLocation()
    {
        $this->location = [
            'company_id' => null,
            'name' => '',
            'code' => '',
            'parent_id' => null,
            'type' => 'branch',
            'description' => '',
            'status' => 'active',
            'is_active' => true,
        ];

        $this->updateMode = false;
        $this->openModal();
    }

    public function edit($id)
    {
        $loc = Location::findOrFail($id);

        $this->location = $loc->only([
            'id',
            'company_id',
            'name',
            'code',
            'parent_id',
            'type',
            'description',
            'status',
            'is_active',
        ]);

        $this->updateMode = true;
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        Location::create($this->location);

        $this->closeModal();
        session()->flash('message', 'Location created successfully.');
    }

    public function update()
    {
        $this->validate();

        $loc = Location::findOrFail($this->location['id'] ?? null);

        $loc->update([
            'company_id' => $this->location['company_id'] ?? null,
            'name' => $this->location['name'] ?? '',
            'code' => $this->location['code'] ?? null,
            'parent_id' => $this->location['parent_id'] ?? null,
            'type' => $this->location['type'] ?? 'branch',
            'description' => $this->location['description'] ?? null,
            'status' => $this->location['status'] ?? 'active',
            'is_active' => $this->location['is_active'] ?? true,
        ]);

        $this->closeModal();
        session()->flash('message', 'Location updated successfully.');
    }

    public function delete()
    {
        Location::findOrFail($this->deleteId)->delete();
        $this->closeModal();
        session()->flash('message', 'Location deleted successfully.');
    }

    public function render()
    {
        $baseQuery = Location::query();

        $locationsCount = $baseQuery->clone()->count();
        $activeLocationsCount = $baseQuery->clone()->where('status', 'active')->count();
        $inactiveLocationsCount = $baseQuery->clone()->where('status', 'inactive')->count();

        $query = $baseQuery->clone()->with('parent');

        if ($this->search !== '') {
            $search = $this->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $query = $this->applyFilters($query, $this->model);

        $locations = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $allLocations = Location::orderBy('name')->get();
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return view('business::livewire.locations.index', [
            'locations' => $locations,
            'allLocations' => $allLocations,
            'companies' => $companies,
            'locationsCount' => $locationsCount,
            'activeLocationsCount' => $activeLocationsCount,
            'inactiveLocationsCount' => $inactiveLocationsCount,
            'model' => $this->model,
        ]);
    }
}
