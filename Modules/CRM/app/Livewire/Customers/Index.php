<?php

namespace Modules\CRM\Livewire\Customers;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\CRM\Models\Customer;

class Index extends Component
{
    use WithFileUploads, WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $secondaryLang;

    /** @var Customer */
    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
        'filters' => ['except' => []],
    ];

    public $mediaComponentNames = ['avatar'];

    public function mount()
    {

        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'reference', 'name', 'email'];

        $this->model = new Customer;
        $this->initFilters($this->model);
        $this->secondaryLang = system_setting('secondary_language', 'ar');
    }

    public function delete()
    {

        Customer::find($this->deleteId)->delete();
        session()->flash('message', 'Customer deleted successfully.');
    }

    public function render()
    {

        // $roles = Role::all();
        $customersCount = Customer::count();
        $activeCustomersCount = Customer::where('status', 'active')->count();
        $inactiveCustomersCount = Customer::where('status', 'inactive')->count();

        $query = Customer::with('customerGroup')
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });

        $query = $this->applyFilters($query, $this->model);

        $customers = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('crm::livewire.customers.index', compact('customers', 'customersCount', 'activeCustomersCount', 'inactiveCustomersCount'));
    }
}
