<?php

namespace Modules\Selling\Livewire\SalesInvoices;

use App\Livewire\WithAutoComplete;
use App\Livewire\WithModalTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Business\Models\Tax;
use Modules\CRM\Models\Customer;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\Unit;
use Modules\Selling\Models\SalesInvoice;
use Modules\Selling\Models\SalesInvoiceItem;
use Modules\Selling\Models\SalesOrder;
use Throwable;

class Edit extends Component
{
    use WithAutoComplete, WithModalTrait;

    public $salesInvoiceId;

    // Sales fields
    public $reference;

    public $customer_id;

    public $sales_order_id;

    public $subtotal = 0;

    public $tax_id = 2;

    public $tax_name;

    public $tax = 0;

    public $total = 0;

    public $paid_amount = 0;

    public $due_amount = 0;

    public $status = 'Pending';

    public $invoice_date;

    public $due_date;

    public $currency = 'SAR';

    public $currency_rate = 1;

    // Item fields
    public $items = [];

    public $item_id;

    public $name;

    public $description;

    public $quantity;

    public $unit;

    public $price;

    public $total_price;

    public $discount_type;

    public $discount_rate;

    public $discount = 0;

    public $is_rental = 0;

    public $rental_start_at;

    public $rental_end_at;

    public $editIndex = '';

    // Autocomplete refs
    public $customer_reference;

    public $sales_order_reference;

    public $item_reference;

    public $customerSuggestions = [];

    public $customerSuggestionsList = 'hidden';

    public $salesOrderSuggestions = [];

    public $salesOrderSuggestionsList = 'hidden';

    public $itemSuggestions = [];

    public $itemSuggestionsList = 'hidden';

    public function mount($salesInvoiceId)
    {
        $this->salesInvoiceId = $salesInvoiceId;
        $invoice = SalesInvoice::with('items')->findOrFail($salesInvoiceId);
        $this->customer_reference = $invoice->customer->reference ?? '';
        // Populate fields
        $this->reference = $invoice->reference;
        $this->customer_id = $invoice->customer_id;
        $this->sales_order_id = $invoice->sales_order_id;
        $this->subtotal = $invoice->subtotal;
        $this->tax_id = $invoice->tax_id;
        $this->tax_name = $invoice->tax_name;
        $this->tax = $invoice->tax;
        $this->total = $invoice->total;
        $this->paid_amount = $invoice->paid_amount;
        $this->due_amount = $invoice->due_amount;
        $this->status = $invoice->status;
        $this->invoice_date = $invoice->invoice_date;
        $this->due_date = $invoice->due_date;
        $this->currency = $invoice->currency;
        $this->currency_rate = $invoice->currency_rate;

        foreach ($invoice->items as $invItem) {
            $item = [
                'item_id' => $invItem->item_id,
                'quantity' => $invItem->quantity,
                'unit' => $invItem->unit,
                'name' => $invItem->name,
                'description' => $invItem->description,
                'price' => $invItem->price,
                'total_price' => $invItem->total_price,
                'discount_type' => $invItem->discount_type ?? 'fixed',
                'discount_rate' => $invItem->discount_rate ?? 0,
                'discount' => $invItem->discount ?? 0,
                'subtotal' => $invItem->subtotal ?? 0,
                'tax_id' => $invItem->tax_id ?? null,
                'tax_name' => $invItem->tax_name ?? null,
                'tax_rate' => $invItem->tax_rate ?? 0,
                'tax_amount' => $invItem->tax ?? 0,
                'total' => $invItem->total,
                'is_rental' => $invItem->is_rental,
                'rental_start_at' => $invItem->rental_start_at,
                'rental_end_at' => $invItem->rental_end_at,
            ];

            $this->items[] = $item;
        }

        $this->calculateTotal();
    }

    protected $rules = [
        'reference' => 'string|nullable',
        'customer_id' => 'required|exists:customers,id',
        'subtotal' => 'required|numeric|min:0',
        'tax' => 'nullable|numeric|min:0',
        'total' => 'required|numeric|min:0',
        'paid_amount' => 'nullable|numeric|min:0',
        'due_amount' => 'nullable|numeric|min:0',
        'status' => 'required',
        'invoice_date' => 'required|date',
        'due_date' => 'required|date',
        'currency' => 'required',
        'currency_rate' => 'required',
        'sales_order_id' => 'nullable',
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
        'items.*.total' => 'nullable|numeric|min:0',
    ];

    public function openAddItemModal()
    {
        $this->openModal();
    }

    public function updatedCustomerReference($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            $this->customerSuggestions = [];
            $this->customerSuggestionsList = 'hidden';

            return;
        }

        $this->customerSuggestions = $this->getSuggestions(Customer::class, $value, ['reference', 'name']);
        if ($this->customerSuggestions) {
            $this->customerSuggestionsList = 'show';
        }
    }

    public function updatedSalesOrderReference($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            $this->salesOrderSuggestions = [];
            $this->salesOrderSuggestionsList = 'hidden';

            return;
        }

        $this->salesOrderSuggestions = $this->getSuggestions(SalesOrder::class, $value, ['reference']);
        if ($this->salesOrderSuggestions) {
            $this->salesOrderSuggestionsList = 'show';
        }
    }

    public function updatedItemReference($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            $this->itemSuggestions = [];
            $this->itemSuggestionsList = 'hidden';

            return;
        }

        $this->itemSuggestions = $this->getSuggestions(Item::class, $value, ['reference', 'name']);
        if ($this->itemSuggestions) {
            $this->itemSuggestionsList = 'show';
        }
    }

    public function selectCustomer($reference)
    {
        $this->customer_reference = $reference;
        $this->customerSuggestionsList = 'hidden';

        $customer = Customer::where('reference', $reference)->first();
        if ($customer) {
            $this->customer_id = $customer->id;
        }
    }

    public function selectSalesOrder($reference)
    {
        $this->sales_order_reference = $reference;
        $this->salesOrderSuggestionsList = 'hidden';
        $salesOrder = SalesOrder::where('reference', $reference)->first();
        $this->sales_order_id = $salesOrder->id ?? null;
    }

    public function selectItem($reference)
    {
        $this->item_reference = $reference;
        $this->itemSuggestionsList = 'hidden';

        $item = Item::where('reference', $reference)->first();
        if (! $item) {
            return;
        }

        $this->item_id = $item->id;
        $this->name = $item->name;
        $this->description = $item->getTranslation('name', 'ar');
        $this->price = $item->price('sell')->price ?? 0;
        $this->quantity = 1;
        $this->discount_type = 'fixed';
        $this->discount_rate = 0;
    }

    public function editItem($index)
    {
        $item = $this->items[$index];
        $this->editIndex = $index;

        $this->item_id = $item['item_id'];
        $this->name = $item['name'];
        $this->description = $item['description'];
        $this->quantity = $item['quantity'];
        $this->unit = $item['unit'];
        $this->price = $item['price'];
        $this->discount_type = $item['discount_type'] ?? 'fixed';
        $this->discount_rate = $item['discount_rate'] ?? 0;
        $this->tax_id = $item['tax_id'] ?? null;

        $this->openModal();
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

        $this->due_amount = $this->total - $this->paid_amount;
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

        $total_before_discount = $this->quantity * $this->price;
        $discount = $this->discount_type == 'percent'
            ? ($total_before_discount * $this->discount_rate) / 100
            : $this->discount_rate;
        $subtotal = $total_before_discount - $discount;

        $tax = Tax::find($this->tax_id);
        $tax_amount = ($tax->rate * $subtotal) / 100;
        $total = $subtotal + $tax_amount;

        $item = [
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'total_price' => $this->price * $this->quantity,
            'discount_type' => $this->discount_type,
            'discount_rate' => $this->discount_rate,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'tax_id' => $tax->id ?? null,
            'tax_name' => $tax->name ?? null,
            'tax_rate' => $tax->rate ?? 0,
            'tax_amount' => $tax_amount,
            'total' => $total,
            'is_rental' => $this->is_rental,
            'rental_start_at' => $this->rental_start_at,
            'rental_end_at' => $this->rental_end_at,
        ];

        if ($this->editIndex === '') {
            $this->items[] = $item;
        } else {
            $this->items[$this->editIndex] = $item;
        }

        $this->editIndex = '';
        $this->resetItemFields();
        $this->calculateTotal();
        $this->closeModal();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->calculateTotal();
    }

    public function resetItemFields()
    {
        $this->item_id = null;
        $this->name = null;
        $this->description = null;
        $this->quantity = null;
        $this->unit = null;
        $this->price = null;
        $this->total_price = null;
        $this->tax_id = null;
        $this->discount_type = null;
        $this->discount_rate = null;
        $this->discount = null;
    }

    // ---------- save ----------
    public function update()
    {
        $validated = $this->validate();
        $validated['total_price'] = $this->total_price;

        try {
            DB::transaction(function () use ($validated) {
                $salesInvoice = SalesInvoice::findOrFail($this->salesInvoiceId);
                $salesInvoice->update($validated);

                // remove old items
                $salesInvoice->items()->delete();

                // insert new items
                foreach ($this->items as $item) {
                    SalesInvoiceItem::create([
                        'invoice_id' => $salesInvoice->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'price' => $item['price'],
                        'total_price' => $item['total_price'],
                        'discount_type' => $item['discount_type'] ?? 'fixed',
                        'discount_rate' => $item['discount_rate'] ?? 0,
                        'discount' => $item['discount'] ?? 0,
                        'subtotal' => $item['subtotal'] ?? 0,
                        'tax_id' => $item['tax_id'] ?? null,
                        'tax_rate' => $item['tax_rate'] ?? 0,
                        'tax_name' => $item['tax_name'] ?? null,
                        'tax' => $item['tax_amount'] ?? 0,
                        'total' => $item['total'],
                        'name' => $item['name'] ?? null,
                        'description' => $item['description'] ?? null,
                        'is_rental' => $item['is_rental'],
                        'rental_start_at' => $item['rental_start_at'],
                        'rental_end_at' => $item['rental_end_at'],
                    ]);
                }
            });

            session()->flash('message', 'Sales Invoice updated successfully!');

            return redirect()->route('admin.selling.sales-invoices.index');
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'Failed to update Sales Invoice. Nothing was saved.');

            return back()->withInput();
        }
    }

    public function render()
    {
        $units = Unit::all();
        $customers = Customer::all();
        $all_items = Item::all();
        $taxes = Tax::all();
        $statuses = SalesInvoice::STATUS_SELECT;

        return view('selling::livewire.sales-invoices.edit', compact('units', 'customers', 'all_items', 'taxes', 'statuses'));
    }
}
