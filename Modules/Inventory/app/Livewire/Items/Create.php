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
use App\Services\AI\TranslationService;

class Create extends Component
{
    use WithFileUploads;

    public array $active_languages = [];

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
        $langs = system_setting('active_languages', ['ar']);
        $this->active_languages = is_string($langs) ? (json_decode($langs, true) ?? [$langs]) : $langs;
    }

    protected function rules1()
    {
        $rules = [
            'reference' => 'nullable|string|max:255|unique:items,reference',
            'name.en' => 'required|string|min:3',
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

        foreach ($this->active_languages as $lang) {
            $rules["name.{$lang}"] = 'nullable|string|min:3';
        }

        return $rules;
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

    public function autoTranslate(TranslationService $translationService)
    {
        $englishName = $this->name['en'] ?? null;
        if (empty(trim($englishName))) {
            session()->flash('error', 'Please enter the Item Name (English) first.');
            return;
        }

        foreach ($this->active_languages as $lang) {
            if ($lang === 'en') continue;

            // Only translate if empty
            if (!empty(trim($this->name[$lang] ?? ''))) continue;

            try {
                $translatedText = $translationService->translate($englishName, $lang, 'product name');
                if (!empty($translatedText)) {
                    $this->name[$lang] = $translatedText;
                }
            } catch (\Exception $e) {
                // Flash an error and continue
                session()->flash('error', 'Translation failed for ' . $lang . ': ' . $e->getMessage());
            }
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
            $names = ['en' => $this->name['en'] ?? null];
            foreach ($this->active_languages as $lang) {
                $names[$lang] = $this->name[$lang] ?? ($this->name['en'] ?? null);
            }

            $item = Item::create([
                'reference' => $this->reference,
                'type' => $this->type,
                'name' => $names,
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
