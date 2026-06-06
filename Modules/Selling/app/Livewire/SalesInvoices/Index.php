<?php

namespace Modules\Selling\Livewire\SalesInvoices;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Selling\Models\SalesInvoice;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public int $perPage;

    public $salesInvoiceId;

    public $reference;

    public $customer_id;

    public $issued_at;

    public $status;

    public $total;

    public $updateMode = false;

    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
        'filters' => ['except' => []],
    ];

    protected function rules()
    {
        return [
            'reference' => 'required|min:3|unique:sales_invoices,reference,'.$this->salesInvoiceId,
            'customer_id' => 'required|exists:customers,id',
            'issued_at' => 'required|date',
            'status' => 'required|in:unpaid,paid,partially_paid,overdue',
            'total' => 'required|numeric|min:0',
        ];
    }

    public $customerId;

    public function mount($customerId = null)
    {
        $this->customerId = $customerId;
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['reference', 'customer_id', 'issued_at', 'total', 'status'];

        $this->model = new SalesInvoice;
        $this->initFilters($this->model);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $this->reference = '';
        $this->customer_id = '';
        $this->issued_at = '';
        $this->status = '';
        $this->total = '';
        $this->salesInvoiceId = null;
        $this->updateMode = false;
    }

    public function delete()
    {
        $salesInvoice = SalesInvoice::find($this->deleteId);
        foreach ($salesInvoice->items as $item) {
            $item->delete();
        }
        $salesInvoice->delete();
        $this->deleteId = '';
        session()->flash('message', 'Sales Invoice deleted successfully.');
    }

    public function render()
    {
        $query = SalesInvoice::query();

        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_id', 'like', '%'.$this->search.'%');
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $salesInvoices = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('selling::livewire.sales-invoices.index', compact('salesInvoices'));
    }
}
