<?php

namespace Modules\Inventory\Livewire\Items;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Business\Models\BusinessPartner;
use Modules\Business\Models\Department;
use Modules\Business\Models\Location;
use Modules\Business\Models\Sponsor;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;

use function system_setting;

class Create extends Component
{
    use WithFileUploads;

    public $second_lang;

    public $reference;

    public $type = 'product';

    public $product_id;

    public $name = [];

    public $description;

    public $category_id;

    public $brand_id;

    public $model_number;

    public $track_inventory = false;

    public $is_serialized = false;

    public $has_variants = false;

    public $status;

    public $sponsor_id;

    public $model_year;

    public $serial_number;

    public $plate_number;

    public $short_description;

    public $warranty_months;

    public $icon_class;

    public $newBrandName;

    public $newCategoryName;

    public $newCategoryParentId;

    public $sell_price;

    public $purchase_price;

    public string $currency_code = 'SAR';

    public int $step = 1;

    public $item;

    public $business_partners = [];

    public $partner_percentages = [];

    public $primary_photo;

    public $customFields = [];

    public $customValues = []; // to store dynamic input values

    protected $queryString = ['step' => ['except' => 1], 'type' => ['except' => 'product'], 'product_id' => ['except' => null]];

    public function mount()
    {
        if (request()->query('type')) {
            $this->type = request()->query('type');
        }
        if (request()->query('type') === 'spare_part') {
            $this->track_inventory = true;
        }
        if (request()->query('product_id')) {
            $this->product_id = request()->query('product_id');
        }
        // ✅ Fetch the secondary language (e.g., 'ar' or 'ur')
        $this->second_lang = system_setting('secondary_language', 'en');
    }

    protected function rules1()
    {
        return [
            'reference' => 'nullable|string|max:255|unique:items,reference',
            'name.en' => 'required|string|min:3',
            "name.{$this->second_lang}" => 'nullable|string|min:3',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:item_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'track_inventory' => 'boolean',
            'is_serialized' => 'boolean',
            'sponsor_id' => 'nullable|exists:sponsors,id',
            'model_number' => 'nullable|required_if:type,part|string',
            'model_year' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'plate_number' => 'nullable|string',
            'status' => 'required|string',
            'warranty_months' => 'nullable|integer|min:0',
            'primary_photo' => 'nullable|image|max:1024', // validation for photo
        ];
    }

    public function updatedName($value, $key)
    {
    }

    public function updatedReference($value)
    {
        if ($this->type === 'spare_part') {
            $this->model_number = $value;
        }
    }

    public function saveStep1()
    {
        try {
            $this->validate($this->rules1());
            if (is_string($this->reference)) {
                $this->reference = trim($this->reference);
            }
            if ($this->reference === '') {
                $this->reference = null;
            }
            $item = Item::create([
                'reference' => $this->reference,
                'type' => $this->type,
                'name' => [
                    'en' => $this->name['en'],
                    $this->second_lang => $this->name[$this->second_lang] ?? $this->name['en'],
                ],
                'category_id' => $this->category_id,
                'model_number' => $this->model_number,
                'track_inventory' => (bool) $this->track_inventory,
                'is_serialized' => (bool) $this->is_serialized,
                'has_variants' => (bool) $this->has_variants,
                'brand_id' => $this->brand_id,
                'description' => $this->description,
                'warranty_months' => $this->warranty_months,
                'status' => $this->status,
            ]);

            if ($this->primary_photo) {
                $item->addMedia($this->primary_photo->getRealPath())->toMediaCollection('primary_photo');
            }

            $this->item = $item;

            if ($this->type === 'spare_part' && $this->product_id) {
                $product = Item::where('type', 'product')->find($this->product_id);
                if ($product) {
                    $product->spareParts()->syncWithoutDetaching([$item->id]);
                }
            }

            session()->flash('message', 'Item saved successfully in previous step.');

            return redirect()->route('inventory.items.edit', ['item' => $item->id, 'step' => 2]);
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: '.$e->getMessage());
            // Log the exception for debugging purposes
            \Log::error('Error in saveStep1 (Create.php): '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());
        }
    }

    public function addBrand()
    {
        Brand::create(['name' => $this->newBrandName]);

        // Update brands list and reset new brand name
        $this->newBrandName = '';
    }

    public function addCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|min:2',
            'newCategoryParentId' => 'nullable|integer|exists:item_categories,id',
        ]);

        ItemCategory::create([
            'name' => $this->newCategoryName,
            'parent_id' => $this->newCategoryParentId,
        ]);

        $this->newCategoryName = '';
        $this->newCategoryParentId = null;
    }

    public function addPartner()
    {
        $this->business_partners[] = '';
        $this->partner_percentages[] = 0;
    }

    public function removePartner($index)
    {
        unset($this->business_partners[$index]);
        unset($this->partner_percentages[$index]);
        $this->business_partners = array_values($this->business_partners);
        $this->partner_percentages = array_values($this->partner_percentages);
    }

    public function render()
    {
        $categories = ItemCategory::all();
        $parent_categories = ItemCategory::with('children')->whereNull('parent_id')->orWhere('parent_id', 0)->get();
        $sponsors = Sponsor::all();
        $businessPartners = BusinessPartner::all();
        $brands = Brand::all();

        return view('inventory::livewire.items.create', compact('categories', 'parent_categories', 'businessPartners', 'sponsors', 'brands'));
    }
}
