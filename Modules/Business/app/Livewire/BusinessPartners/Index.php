<?php

namespace Modules\Business\Livewire\BusinessPartners;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\BusinessPartner;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    public $search = '';

    public $name;

    public $email;

    public $partnerId;

    public $updateMode = false;

    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:business_partners,email',
    ];

    public $perPage = 10; // Pagination limit

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'asc'; // Default sort direction
        $this->perPage = 10; // Default pagination limit
        $this->orderable = ['id', 'name', 'email'];
        $this->model = new BusinessPartner;
        $this->initFilters($this->model);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $baseQuery = BusinessPartner::query();

        $partnersCount = $baseQuery->clone()->count();

        $query = $baseQuery->clone();

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $partners = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('business::livewire.business-partners.index', [
            'partners' => $partners,
            'partnersCount' => $partnersCount,
            'model' => $this->model,
        ]);
    }

    public function store()
    {
        $this->validate();

        BusinessPartner::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);
        $this->closeModal();
        session()->flash('message', 'Business Partner created successfully.');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->updateMode = false;
    }

    public function edit($id)
    {
        $partner = BusinessPartner::findOrFail($id);
        $this->partnerId = $id;
        $this->name = $partner->name;
        $this->email = $partner->email;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:business_partners,email,'.$this->partnerId,
        ]);

        $partner = BusinessPartner::findOrFail($this->partnerId);
        $partner->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);
        session()->flash('message', 'Role updated successfully.');
        $this->closeModal();
        $this->resetInputFields();

    }

    public function delete()
    {
        BusinessPartner::findOrFail($this->deleteId)->delete();
        $this->closeModal();
        session()->flash('message', 'Business Partner deleted successfully.');
    }
}
