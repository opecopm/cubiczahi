<?php

namespace Modules\Selling\Livewire\SalesOrders;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Selling\Models\SalesOrder;
use Modules\Selling\Models\SalesOrderItem;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public int $perPage;

    public $salesOrderId;

    public $reference;

    public $customer_id;

    public $order_date;

    public $status;

    public $total;

    public $updateMode = false;

    public $model;

    public $customerId;

    public bool $isEmbedded = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
        'filters' => ['except' => []],
    ];

    protected function rules()
    {
        return [
            'reference' => 'required|min:3|unique:sales_orders,reference,'.$this->salesOrderId,
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(\Modules\Selling\Models\SalesOrder::STATUS_SELECT)),
            'total' => 'required|numeric|min:0',
        ];
    }

    public function mount($customerId = null)
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['reference', 'customer_id', 'order_date', 'total', 'status'];

        $this->model = new SalesOrder;
        $this->initFilters($this->model);

        if ($customerId) {
            $this->customerId = $customerId;
            $this->filters['customer_id'] = $customerId;
            $this->isEmbedded = true;
        }
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $this->reference = '';
        $this->customer_id = '';
        $this->order_date = '';
        $this->status = '';
        $this->total = '';
        $this->salesOrderId = null;
        $this->updateMode = false;
    }

    public function delete()
    {
        $salesOrder = SalesOrder::find($this->deleteId);

        if ($salesOrder) {
            SalesOrderItem::where('sales_order_id', $salesOrder->id)->delete();
            $salesOrder->delete();

            session()->flash('message', 'Sales Order deleted successfully.');
        } else {
            session()->flash('error', 'Sales Order not found.');
        }

        $this->deleteId = '';
    }

    public function render()
    {
        $query = SalesOrder::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_id', 'like', '%'.$this->search.'%');
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $salesOrders = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('selling::livewire.sales-orders.index', compact('salesOrders'));
    }
}
