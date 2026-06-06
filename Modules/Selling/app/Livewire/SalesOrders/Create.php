<?php

namespace Modules\Selling\Livewire\SalesOrders;

use App\Livewire\WithAutoComplete;
use App\Livewire\WithModalTrait;
use Livewire\Component;
use Modules\Business\Models\Tax;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\ReferenceSchema;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\Unit;
use Modules\Selling\Models\SalesOrder;
use Modules\Selling\Models\SalesOrderItem;

class Create extends Component
{
    use WithAutoComplete, WithModalTrait;

    public $reference;

    public $customer_id;

    public $total_price = 0;

    public $subtotal = 0;

    public $tax_id = 2;

    public $tax_name;

    public $tax = 0;

    public $total = 0;

    public $status = 'draft';

    public $order_date;

    public $delivery_date;

    public $currency = 'SAR';

    public $currency_rate = 1;

    public $items = [];

    public $item_id;

    public $name;

    public $description;

    public $quantity;

    public $unit;

    public $price;

    public $discount_type = 'fixed';

    public $discount_rate = 0;

    public $discount = 0;

    public $editIndex = '';

    public $customer_reference;

    public $item_reference;

    public $customerSuggestions = [];

    public $customerSuggestionsList = 'hidden';

    public $itemSuggestions = [];

    public $itemSuggestionsList = 'hidden';

    public function mount()
    {
        $this->order_date = now()->toDateString();
        $this->delivery_date = now()->addWeek()->toDateString();
    }

    protected function rules()
    {
        return [
            'reference' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'total_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', array_keys(\Modules\Selling\Models\SalesOrder::STATUS_SELECT)),
            'order_date' => 'required|date',
            'delivery_date' => 'required|date',
            'currency' => 'required|string',
            'currency_rate' => 'required|numeric|min:0',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.discount_type' => 'nullable|string|in:percent,fixed',
            'items.*.discount_rate' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
            'items.*.tax_id' => 'required|exists:taxes,id',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.tax_name' => 'nullable|string',
            'items.*.tax' => 'nullable|numeric|min:0',
        ];
    }

    public function updatedCustomerReference($value)
    {
        $this->customerSuggestions = $this->getSuggestions(Customer::class, $value, ['reference', 'name']);
        $this->customerSuggestionsList = $this->customerSuggestions ? 'show' : 'hidden';
    }

    public function updatedItemReference($value)
    {
        $this->itemSuggestions = $this->getSuggestions(Item::class, $value, ['reference', 'name']);
        $this->itemSuggestionsList = $this->itemSuggestions ? 'show' : 'hidden';
    }

    public function selectCustomer($reference)
    {
        $this->customer_reference = $reference;
        $this->customerSuggestionsList = 'hidden';
        $cust = Customer::where('reference', $reference)->first();
        $this->customer_id = $cust->id;
    }

    public function selectItem($reference)
    {
        $this->item_reference = $reference;
        $this->itemSuggestionsList = 'hidden';
        $it = Item::where('reference', $reference)->first();
        $this->item_id = $it->id;
        $this->name = $it->name;
        $this->description = $it->description;
        $this->price = $it->price('sell')->price ?? 0;
        $this->quantity = 1;
        $this->unit = 'Pcs';
        $this->discount_type = 'fixed';
        $this->discount_rate = 0;
        $this->tax_id = 2;
    }

    public function openAddItemModal()
    {
        $this->resetItemFields();
        $this->openModal();
    }

    public function editItem($index)
    {
        $item = $this->items[$index];
        $this->editIndex = $index;
        $this->item_reference = Item::find($item['item_id'])->reference;
        $this->item_id = $item['item_id'];
        $this->name = $item['name'];
        $this->description = $item['description'];
        $this->quantity = $item['quantity'];
        $this->unit = $item['unit'];
        $this->price = $item['price'];
        $this->discount_type = $item['discount_type'];
        $this->discount_rate = $item['discount_rate'];
        $this->tax_id = $item['tax_id'];
        $this->openModal();
    }

    public function addItem()
    {
        $this->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'price' => 'required|numeric|min:0',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'tax_id' => 'required|exists:taxes,id',
            'discount_type' => 'nullable|string|in:percent,fixed',
            'discount_rate' => 'nullable|numeric|min:0',
        ]);

        $amount = $this->quantity * $this->price;
        $discount = $this->discount_type === 'percent'
                     ? ($amount * $this->discount_rate) / 100
                     : $this->discount_rate;
        $subtotal = $amount - $discount;
        $taxObj = Tax::find($this->tax_id);
        $tax_amount = ($taxObj->rate * $subtotal) / 100;
        $total = $subtotal + $tax_amount;

        $row = [
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'total_price' => $amount,
            'discount_type' => $this->discount_type ?? 'fixed',
            'discount_rate' => $this->discount_rate ?? 0,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'tax_id' => $taxObj->id,
            'tax_name' => $taxObj->name,
            'tax_rate' => $taxObj->rate,
            'tax_amount' => $tax_amount,
            'total' => $total,
        ];

        if ($this->editIndex === '') {
            $this->items[] = $row;
        } else {
            $this->items[$this->editIndex] = $row;
        }

        $this->editIndex = '';
        $this->resetItemFields();
        $this->calculateTotal();
        $this->closeModal();
    }

    private function resetItemFields()
    {
        $this->item_id = null;
        $this->item_reference = null;
        $this->name = null;
        $this->description = null;
        $this->quantity = null;
        $this->unit = null;
        $this->price = null;
        $this->tax_id = null;
        $this->discount_type = null;
        $this->discount_rate = null;
        $this->discount = null;
    }

    public function calculateTotal()
    {
        $this->total_price = collect($this->items)->sum(function ($item) {
            return $item['total_price'];
        });
        $this->discount = collect($this->items)->sum(function ($item) {
            return $item['discount'];
        });
        $this->subtotal = collect($this->items)->sum(function ($item) {
            return $item['subtotal'];
        });
        $this->tax = collect($this->items)->sum(function ($item) {
            return $item['tax_amount'];
        });
        $this->total = collect($this->items)->sum(function ($item) {
            return $item['total'];
        });

    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->calculateTotal();
    }

    public function store()
    {
        $validatedData = $this->validate();

        $orderNumber = ReferenceSchema::generate('sales_order');
        $validatedData['reference'] = $orderNumber;

        $salesOrder = SalesOrder::create($validatedData);

        foreach ($this->items as $item) {
            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total_price' => $item['total_price'],
                'discount_type' => $item['discount_type'],
                'discount_rate' => $item['discount_rate'],
                'discount' => $item['discount'],
                'subtotal' => $item['subtotal'],
                'tax_id' => $item['tax_id'],
                'tax_rate' => $item['tax_rate'],
                'tax_name' => $item['tax_name'],
                'tax' => $item['tax_amount'],
                'total' => $item['total'],
                'name' => $item['name'],
                'description' => $item['description'],
            ]);
        }

        session()->flash('message', 'Sales Order created successfully!');

        return redirect()->route('admin.selling.sales-orders.index');
    }

    public function render()
    {
        $units = Unit::all();
        $customers = Customer::all();
        $all_items = Item::all();
        $taxes = Tax::all();
        $statuses = SalesOrder::STATUS_SELECT;

        return view('selling::livewire.sales-orders.create', compact('units', 'customers', 'all_items', 'taxes', 'statuses'));
    }
}
