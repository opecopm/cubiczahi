<?php

namespace Modules\Business\Livewire\Sponsors;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\Sponsor;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    public $search = '';

    public $name;

    public $email;

    public $sponsorId;

    public $updateMode = false;

    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    public $perPage = 10; // Pagination limit

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:sponsors,email',
    ];

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->updateMode = false;
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'asc'; // Default sort direction
        $this->perPage = 10; // Default pagination limit
        $this->orderable = ['id', 'name', 'email'];
        $this->model = new Sponsor;
        $this->initFilters($this->model);
    }

    public function render()
    {
        $baseQuery = Sponsor::query();

        $sponsorsCount = $baseQuery->clone()->count();

        $query = $baseQuery->clone();

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $sponsors = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('business::livewire.sponsors.index', [
            'sponsors' => $sponsors,
            'sponsorsCount' => $sponsorsCount,
            'model' => $this->model,
        ]);
    }

    public function store()
    {
        $this->validate();

        Sponsor::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('message', 'Sponsor created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $this->sponsorId = $id;
        $this->name = $sponsor->name;
        $this->email = $sponsor->email;
        $this->updateMode = true;
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sponsors,email,'.$this->sponsorId,
        ]);

        $sponsor = Sponsor::findOrFail($this->sponsorId);
        $sponsor->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('message', 'Sponsor updated successfully.');
        $this->closeModal();
    }

    public function delete()
    {
        Sponsor::findOrFail($this->deleteId)->delete();
        $this->closeModal();
        session()->flash('message', 'Sponsor deleted successfully.');
    }
}
