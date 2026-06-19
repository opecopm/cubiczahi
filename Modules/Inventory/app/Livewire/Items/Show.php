<?php

namespace Modules\Inventory\Livewire\Items;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Business\Models\Currency;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\CustomField;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCustomValue;
use Modules\Inventory\Models\ItemPrice;

use function system_setting;

class Show extends Component
{
    use WithFileUploads;

    public $item;

    public $purchase_price;

    public $sell_price;

    public $second_lang = 'ar';

    public array $active_languages = [];

    public array $descriptions = [];

    public array $short_descriptions = [];

    public array $seo_title = [];

    public array $seo_description = [];

    public array $seo_keywords = [];

    public $photoModal = false;

    public $primary_photo;

    public $manual_file;

    public $searchModal = false;

    public $sparePartsModal = false;

    public $sparePartSearch = '';

    public $sparePartResults = [];

    public $productsModal = false;

    public $productSearch = '';

    public $productResults = [];

    public $priceModal = false;

    public $priceData = [];

    public $currencies = [];

    public $customers = [];

    public $customerSearch = '';

    public $customerResults = [];

    public $itemSearch = '';

    public $itemResults = [];

    public $type;

    protected $queryString = [
        'type' => ['except' => ''],
    ];

    public function mount($itemId)
    {
        $this->item = Item::findOrFail($itemId);
        $this->type = request()->query('type', $this->item->type);
        $this->purchase_price = $this->item->price('purchase')->price ?? null;
        $this->sell_price = $this->item->price('sell')->price ?? null;

        $langs = system_setting('active_languages', ['ar']);
        $this->active_languages = is_string($langs) ? (json_decode($langs, true) ?? [$langs]) : $langs;
        $this->second_lang = $this->active_languages[0] ?? 'ar';
        $locales = array_values(array_unique(array_merge(['en'], $this->active_languages)));

        foreach ($locales as $locale) {
            $this->descriptions[$locale] = (string) ($this->item->getTranslation('description', $locale, false) ?? '');

            $this->short_descriptions[$locale] = (string) ($this->item->getTranslation('short_description', $locale, false) ?? '');
            $this->seo_title[$locale] = '';
            $this->seo_description[$locale] = '';
            $this->seo_keywords[$locale] = '';
        }

        $seoFieldNames = [];
        foreach ($locales as $locale) {
            $seoFieldNames[] = "seo_title_{$locale}";
            $seoFieldNames[] = "seo_description_{$locale}";
            $seoFieldNames[] = "seo_keywords_{$locale}";
        }

        $seoFields = CustomField::query()
            ->where('module', 'inventory')
            ->where('model', 'item')
            ->whereIn('name', $seoFieldNames)
            ->get(['id', 'name']);

        if ($seoFields->isNotEmpty()) {
            $values = ItemCustomValue::query()
                ->where('item_id', $this->item->id)
                ->whereIn('custom_field_id', $seoFields->pluck('id'))
                ->get(['custom_field_id', 'value'])
                ->keyBy('custom_field_id');

            foreach ($locales as $locale) {
                $titleField = $seoFields->firstWhere('name', "seo_title_{$locale}");
                $descField = $seoFields->firstWhere('name', "seo_description_{$locale}");
                $keywordsField = $seoFields->firstWhere('name', "seo_keywords_{$locale}");

                if ($titleField && isset($values[$titleField->id])) {
                    $this->seo_title[$locale] = (string) ($values[$titleField->id]->value ?? '');
                }
                if ($descField && isset($values[$descField->id])) {
                    $this->seo_description[$locale] = (string) ($values[$descField->id]->value ?? '');
                }
                if ($keywordsField && isset($values[$keywordsField->id])) {
                    $this->seo_keywords[$locale] = (string) ($values[$keywordsField->id]->value ?? '');
                }
            }
        }
    }

    public function openPhotoModal()
    {
        $this->photoModal = true;
    }

    public function closePhotoModal()
    {
        $this->photoModal = false;
    }

    public function updatePrimaryPhoto()
    {
        $this->validate([
            'primary_photo' => 'required|image|max:1024', // 1 MB
        ]);

        if ($this->primary_photo && $this->item) {
            // Clear old photo safely
            $this->item->clearMediaCollection('primary_photo');

            // Store new photo with an explicit name
            $this->item
                ->addMedia($this->primary_photo->getRealPath())
                ->usingFileName(time().'_'.$this->primary_photo->getClientOriginalName())
                ->toMediaCollection('primary_photo');
        }

        // Reset only the modal field, not all Livewire data
        $this->reset('primary_photo');

        // Optional: close modal if you use one
        $this->photoModal = false;

        // Success message
        session()->flash('message', 'Primary photo updated successfully!');
    }

    public function uploadManual()
    {
        $this->validate([
            'manual_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($this->manual_file && $this->item) {
            $this->item->clearMediaCollection('manual');
            $this->item
                ->addMedia($this->manual_file->getRealPath())
                ->usingFileName(time().'_'.$this->manual_file->getClientOriginalName())
                ->toMediaCollection('manual');
        }

        $this->reset('manual_file');
        $this->item->refresh();
        session()->flash('message', 'Manual uploaded successfully!');
    }

    public function deleteManual()
    {
        if (! $this->item) {
            return;
        }

        $this->item->clearMediaCollection('manual');
        $this->item->refresh();
        session()->flash('message', 'Manual deleted successfully!');
    }

    public function openSearchModal()
    {
        $this->resetValidation();
        $this->searchModal = true;
        $this->itemSearch = '';
        $this->itemResults = [];
    }

    public function closeSearchModal()
    {
        $this->searchModal = false;
        $this->itemSearch = '';
        $this->itemResults = [];
    }

    public function openSparePartsModal()
    {
        $this->resetValidation();
        $this->sparePartsModal = true;
        $this->sparePartSearch = '';
        $this->sparePartResults = [];
    }

    public function closeSparePartsModal()
    {
        $this->sparePartsModal = false;
        $this->sparePartSearch = '';
        $this->sparePartResults = [];
    }

    public function openProductsModal()
    {
        $this->resetValidation();
        $this->productsModal = true;
        $this->productSearch = '';
        $this->productResults = [];
    }

    public function closeProductsModal()
    {
        $this->productsModal = false;
        $this->productSearch = '';
        $this->productResults = [];
    }

    /** Show modal for Add/Edit */
    public function openPriceModal($id = null)
    {
        $this->resetValidation();
        $this->customers = Customer::select('id', 'name')->get();
        $this->currencies = Currency::where('status', 'active')
            ->get(['id', 'name', 'code', 'symbol_left', 'symbol_right', 'rate']);
        $this->priceData = [
            'id' => null,
            'price_type' => '',
            'price' => '',
            'currency' => '',
            'currency_rate' => '',
            'customer_id' => null,
            'date_from' => '',
            'date_to' => '',
            'is_default' => false,
        ];

        if ($id) {
            $price = ItemPrice::find($id);
            if ($price) {
                $this->priceData = $price->only([
                    'id', 'price_type', 'price', 'currency', 'currency_rate',
                    'customer_id', 'date_from', 'date_to', 'is_default',
                ]);
            }
            // ✅ Fill customer name for the search box display
            if ($price->price_type === 'selling' && $price->customer) {
                $this->customerSearch = $price->customer->name;
            }
        }

        $this->priceModal = true;
    }

    /** Auto-fill rate when selecting currency */
    public function updatedPriceDataCurrencyId($currencyId)
    {
        $currency = Currency::find($currencyId);
        if ($currency) {
            $this->priceData['currency_rate'] = $currency->rate;
        } else {
            $this->priceData['currency_rate'] = '';
        }
    }

    public function closePriceModal()
    {
        $this->priceModal = false;
    }

    /** Search Customers dynamically */
    public function updatedCustomerSearch()
    {
        if (strlen($this->customerSearch) > 1) {
            $this->customerResults = Customer::where('name', 'like', '%'.$this->customerSearch.'%')
                ->limit(10)
                ->get(['id', 'name']);
        } else {
            $this->customerResults = [];
        }
    }

    /** Select customer */
    public function selectCustomer($id, $name)
    {
        $this->priceData['customer_id'] = $id;
        $this->priceData['customer_name'] = $name;
        $this->customerSearch = $name;
        $this->customerResults = [];
    }

    /** Clear customer */
    public function clearCustomer()
    {
        $this->priceData['customer_id'] = null;
        $this->priceData['customer_name'] = null;
        $this->customerSearch = '';
        $this->customerResults = [];
    }

    /** Save or update price */
    public function savePrice()
    {
        $this->validate([
            'priceData.price_type' => 'required|in:selling,purchase',
            'priceData.price' => 'required|numeric|min:0',
            'priceData.currency' => 'nullable|string|max:5',
            'priceData.currency_rate' => 'nullable|numeric|min:0',
            'priceData.date_from' => 'nullable|date',
            'priceData.date_to' => 'nullable|date|after_or_equal:priceData.date_from',
            'priceData.customer_id' => 'nullable|exists:customers,id',
        ]);

        ItemPrice::updateOrCreate(
            ['id' => $this->priceData['id'] ?? null],
            [
                'item_id' => $this->item->id,
                'price_type' => $this->priceData['price_type'],
                'price' => $this->priceData['price'],
                'currency' => $this->priceData['currency'],
                'currency_rate' => $this->priceData['currency_rate'],
                'vendor_id' => null,
                'customer_id' => $this->priceData['customer_id'],
                'date_from' => $this->priceData['date_from'],
                'date_to' => $this->priceData['date_to'],
                'is_default' => $this->priceData['is_default'] ?? false,
            ]
        );

        $this->priceModal = false;
        session()->flash('message', 'Price added/updated successfully!');
    }

    /** Delete a price entry */
    public function deletePrice($id)
    {
        $price = ItemPrice::find($id);
        if ($price) {
            $price->delete();
            session()->flash('message', 'Price deleted successfully!');
        }
    }

    /** Search items as user types */
    public function updatedItemSearch()
    {
        if (strlen($this->itemSearch) > 1) {
            $this->itemResults = Item::query()
                ->where('type', $this->item->type)
                ->where('name', 'like', '%'.$this->itemSearch.'%')
                ->orWhere('reference', 'like', '%'.$this->itemSearch.'%')
                ->limit(10)
                ->get(['id', 'name', 'reference']);
        } else {
            $this->itemResults = [];
        }
    }

    public function updatedSparePartSearch()
    {
        if (! $this->item || $this->item->type !== 'product') {
            $this->sparePartResults = [];

            return;
        }

        if (strlen($this->sparePartSearch) > 1) {
            $linkedIds = $this->item->spareParts()->pluck('items.id')->toArray();

            $this->sparePartResults = Item::query()
                ->where('type', 'spare_part')
                ->whereNotIn('id', array_merge([$this->item->id], $linkedIds))
                ->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->sparePartSearch.'%')
                        ->orWhere('reference', 'like', '%'.$this->sparePartSearch.'%');
                })
                ->limit(10)
                ->get(['id', 'name', 'reference', 'type']);
        } else {
            $this->sparePartResults = [];
        }
    }

    public function attachSparePart($sparePartId)
    {
        if (! $this->item || $this->item->type !== 'product') {
            return;
        }

        $sparePart = Item::where('type', 'spare_part')->find($sparePartId);
        if (! $sparePart) {
            return;
        }

        $this->item->spareParts()->syncWithoutDetaching([$sparePart->id]);
        $this->item->refresh();

        $this->sparePartSearch = '';
        $this->sparePartResults = [];

        session()->flash('message', 'Spare part linked successfully!');
    }

    public function updatedProductSearch()
    {
        if (! $this->item || $this->item->type !== 'spare_part') {
            $this->productResults = [];

            return;
        }

        if (strlen($this->productSearch) > 1) {
            $linkedIds = $this->item->products()->pluck('items.id')->toArray();

            $this->productResults = Item::query()
                ->where('type', 'product')
                ->whereNotIn('id', array_merge([$this->item->id], $linkedIds))
                ->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->productSearch.'%')
                        ->orWhere('reference', 'like', '%'.$this->productSearch.'%');
                })
                ->limit(10)
                ->get(['id', 'name', 'reference', 'type']);
        } else {
            $this->productResults = [];
        }
    }

    public function attachProduct($productId)
    {
        if (! $this->item || $this->item->type !== 'spare_part') {
            return;
        }

        $product = Item::where('type', 'product')->find($productId);
        if (! $product) {
            return;
        }

        $this->item->products()->syncWithoutDetaching([$product->id]);
        $this->item->refresh();

        $this->productSearch = '';
        $this->productResults = [];

        session()->flash('message', 'Product linked successfully!');
    }

    /** Redirect to the selected item */
    public function redirectToItem($id)
    {
        $this->itemResults = [];
        $this->itemSearch = '';
        $this->searchModal = false;

        return redirect()->route('admin.inventory.items.show', $id);
    }

    public function render()
    {
        return view('inventory::livewire.items.show');
    }
}
