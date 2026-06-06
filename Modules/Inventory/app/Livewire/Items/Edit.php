<?php

namespace Modules\Inventory\Livewire\Items;

use App\Livewire\WithAutoComplete;
use App\Livewire\WithModalTrait;
use App\Traits\HasMediaManagement;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Business\Models\BusinessPartner;
use Modules\Business\Models\Currency;
use Modules\Business\Models\Department;
use Modules\Business\Models\Location;
use Modules\Business\Models\Sponsor;
use Modules\Global\Models\CustomField;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemCustomValue;
use Modules\Inventory\Models\ItemImage;
use Modules\Inventory\Models\ItemPrice;
use Modules\Inventory\Models\ItemVariant;
use Modules\Inventory\Models\VariantImage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use function system_setting;

class Edit extends Component
{
    use WithAutoComplete, WithFileUploads, WithModalTrait, HasMediaManagement;

    public $second_lang;

    public $type;

    public $name = [];

    public $name_en;

    public $description;

    public $category_id;

    public $brand_id;

    public $track_inventory = false;

    public $is_serialized = false;

    public $has_variants = false;

    public $status;

    public $sponsor_id;

    public $model_number;

    public $model_year;

    public $serial_number;

    public $short_description;

    public $warranty_months;

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

    public $documents = [];

    public $newDocumentName;

    public $newDocumentFile;

    public $newDocumentExpiryDate;

    public $deleteDocId;

    public $primary_photo;

    public $photos = [];

    public $newPhotosFiles = [];

    public $newPhotoTitle;

    public $deletePhotoId;

    public $editDocumentId = null;

    public $updateMode = false;

    public $icon_class;

    public $slug;

    public bool $slugManuallyEdited = false;

    public array $descriptions = [];

    public array $short_descriptions = [];

    public array $seo_title = [];

    public array $seo_description = [];

    public array $seo_keywords = [];

    // Customer
    public $customer_id;

    // price period
    public $date_from;

    public $date_to;

    public $showDocModal = false;

    public $customFields = [];

    public $customValues = []; // to store dynamic input values

    protected $queryString = ['step' => ['except' => 1]];

    private function getTranslatableString($model, string $field, string $locale): string
    {
        $value = '';
        if (is_object($model) && method_exists($model, 'getTranslation')) {
            $value = (string) ($model->getTranslation($field, $locale, false) ?? '');
        }

        $value = trim($value);
        if ($value !== '') {
            return $value;
        }

        if (! is_object($model) || ! isset($model->{$field})) {
            return '';
        }

        $raw = $model->{$field};
        if (! is_string($raw)) {
            return '';
        }

        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $candidate = $decoded[$locale] ?? '';

            return is_string($candidate) ? trim($candidate) : '';
        }

        return $locale === 'en' ? $raw : '';
    }

    public function mount($itemId)
    {
        $this->second_lang = system_setting('secondary_language', 'en');

        $item = Item::findOrFail($itemId);
        $this->item = $item;
        $this->name['en'] = $item->getTranslation('name', 'en');
        $this->name[$this->second_lang] = $item->getTranslation('name', $this->second_lang);
        $this->type = $item->type;
        $this->category_id = $item->category_id;
        $this->brand_id = $item->brand_id;
        $this->model_number = $item->model_number;
        $this->status = $item->status;
        $this->track_inventory = (bool) $item->track_inventory;
        $this->is_serialized = (bool) $item->is_serialized;
        $this->has_variants = (bool) $item->has_variants;
        $this->slug = $item->slug;
        $this->slugManuallyEdited = filled($item->slug);
        $locales = array_values(array_unique(['en', $this->second_lang]));
        foreach ($locales as $locale) {
            $this->descriptions[$locale] = $this->getTranslatableString($item, 'description', $locale);
            $this->short_descriptions[$locale] = $this->getTranslatableString($item, 'short_description', $locale);
        }
        $seoFieldNames = [];
        foreach ($locales as $locale) {
            $seoFieldNames[] = "seo_title_{$locale}";
            $seoFieldNames[] = "seo_description_{$locale}";
            $seoFieldNames[] = "seo_keywords_{$locale}";
        }

        $seoFields = CustomField::forModuleModel('inventory', 'item')
            ->whereIn('name', $seoFieldNames)
            ->get(['id', 'name'])
            ->keyBy('id');

        $seoValues = $seoFields->isEmpty()
            ? collect()
            : ItemCustomValue::query()
                ->where('item_id', $item->id)
                ->whereIn('custom_field_id', $seoFields->keys())
                ->get(['custom_field_id', 'value'])
                ->keyBy('custom_field_id');

        foreach ($locales as $locale) {
            $this->seo_title[$locale] = '';
            $this->seo_description[$locale] = '';
            $this->seo_keywords[$locale] = '';
        }

        foreach ($seoValues as $customFieldId => $row) {
            $fieldName = $seoFields[$customFieldId]?->name ?? null;
            if (! $fieldName) {
                continue;
            }

            foreach ($locales as $locale) {
                if ($fieldName === "seo_title_{$locale}") {
                    $this->seo_title[$locale] = (string) ($row->value ?? '');
                } elseif ($fieldName === "seo_description_{$locale}") {
                    $this->seo_description[$locale] = (string) ($row->value ?? '');
                } elseif ($fieldName === "seo_keywords_{$locale}") {
                    $this->seo_keywords[$locale] = (string) ($row->value ?? '');
                }
            }
        }

        // Initialize MediaManager for item images
        $this->initializeMediaManager('item', $item->id, 'image', [
            'title' => 'Product Images',
            'description' => 'Upload product images for the gallery',
            'allowMultiple' => true,
            'allowPrimary' => true,
            'acceptedFormats' => 'image/*',
        ]);

        $this->warranty_months = $item->warranty_months;
        $sellPrice = $item->price('sell');
        $this->sell_price = $sellPrice->price ?? 0;
        if (! empty($sellPrice?->currency)) {
            $this->currency_code = (string) $sellPrice->currency;
        }
        $this->purchase_price = 0;
        $purchasePrice = $item->price('purchase');
        if ($purchasePrice) {
            $this->purchase_price = $purchasePrice->price ?? 0;
            if (empty($this->currency_code) && ! empty($purchasePrice->currency)) {
                $this->currency_code = (string) $purchasePrice->currency;
            }
        }
    }
    public function updatedNameEn(string $value): void
    {
        if (! $this->slugManuallyEdited) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug(string $value): void
    {
        $this->slugManuallyEdited = filled($value);
        $this->slug = Str::slug($value);
    }

    public function regenerateSlug(): void
    {
        $this->slug = Str::slug($this->name['en'] ?? '');
        $this->slugManuallyEdited = false;
    }

    protected function rules1()
    {
        return [
            'name.en' => 'required|string|min:3',
            "name.{$this->second_lang}" => 'nullable|string|min:3',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:item_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'sell_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3',
            'track_inventory' => 'boolean',
            'is_serialized' => 'boolean',
            'sponsor_id' => 'nullable|exists:sponsors,id',
            'status' => 'required|string',
            'primary_photo' => 'nullable|image|max:1024',
            'model_number' => 'nullable|string',
            'model_year' => 'nullable|string',
            'serial_number' => 'nullable|string',
        ];
    }

    public function updatedName($value, $key)
    {
    }

    public function saveStep1()
    {
        try {
            $this->validate($this->rules1());
            $data = [
                'type' => $this->type,
                'name' => [
                    'en' => $this->name['en'],
                    $this->second_lang => $this->name[$this->second_lang] ?? $this->name['en'],
                ],
                'category_id' => $this->category_id,
                'brand_id' => $this->brand_id,
                'track_inventory' => (bool) $this->track_inventory,
                'is_serialized' => (bool) $this->is_serialized,
                'has_variants' => (bool) $this->has_variants,
                'warranty_months' => $this->warranty_months,
                'status' => $this->status,
            ];

            if (Schema::hasColumn('items', 'icon_class')) {
                $data['icon_class'] = $this->icon_class;
            }
            if (Schema::hasColumn('items', 'slug')) {
                $data['slug'] = $this->slug ?: null;
            }

            $this->item->update($data);

            $currency = Currency::where('code', $this->currency_code)->first();
            $rate = $currency->rate ?? 1.0;
            $from = $this->date_from ?: null;
            $to = $this->date_to ?: null;

            ItemPrice::updateOrCreate(
                [
                    'item_id' => $this->item->id,
                    'price_type' => 'sell',
                    'customer_id' => $this->customer_id,
                    'date_from' => $from,
                ],
                [
                    'price' => $this->sell_price ?? 0,
                    'currency' => $this->currency_code,
                    'currency_rate' => $rate,
                    'date_to' => $to,
                    'is_default' => 1,
                ]
            );

            ItemPrice::updateOrCreate(
                [
                    'item_id' => $this->item->id,
                    'price_type' => 'purchase',
                    'vendor_id' => null,
                ],
                [
                    'price' => $this->purchase_price ?? 0,
                    'currency' => $this->currency_code,
                    'currency_rate' => $rate,
                    'is_default' => 1,
                ]
            );

            if ($this->primary_photo) {
                $this->item->clearMediaCollection('primary_photo'); // Clear existing media
                $this->item->addMedia($this->primary_photo->getRealPath())->toMediaCollection('primary_photo');
            }

            session()->flash('message', 'Item saved successfully in previous step.');
            $this->step = 2;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: '.$e->getMessage());
            // Log the exception for debugging purposes
            \Log::error('Error in saveStep1 (Edit.php): '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());
        }
    }

    protected $rules2 = [
        'sell_price' => 'required|numeric|min:0',
        'purchase_price' => 'required|numeric|min:0',
        'currency_code' => 'required|string|size:3',
        'customer_id' => 'nullable|integer',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
    ];

    public function saveStep2()
    {
        $this->validate($this->rules2);

        $currency = Currency::where('code', $this->currency_code)->first();
        $rate = $currency->rate ?? 1.0;

        // normalize dates; if neither provided, leave them null (means always valid)
        $from = $this->date_from ?: null;
        $to = $this->date_to ?: null;

        // --- SELLING (customer) price ---
        if ($this->sell_price !== null) {
            // if you want a generic/default selling price (no customer), leave customer_id null
            ItemPrice::updateOrCreate(
                [
                    'item_id' => $this->item->id,
                    'price_type' => 'sell',
                    'customer_id' => $this->customer_id, // may be null (default selling)
                    'date_from' => $from,              // part of the unique key for history
                ],
                [
                    'price' => $this->sell_price ?? 0,
                    'currency' => $this->currency_code,
                    'currency_rate' => $rate,
                    'date_to' => $to,
                    'is_default' => 1,
                ]
            );
        }

        // --- PURCHASE (vendor) price ---
        if ($this->purchase_price !== null) {
            ItemPrice::updateOrCreate(
                [
                    'item_id' => $this->item->id,
                    'price_type' => 'purchase',
                    'vendor_id' => null,
                ],
                [
                    'price' => $this->purchase_price ?? 0,
                    'currency' => $this->currency_code,
                    'currency_rate' => $rate,
                    'is_default' => 1,
                ]
            );
        }

        $this->step = 3;

        session()->flash('message', 'Item prices saved.');
    }

    /* step 3 – Images - Now handled by MediaManager component */

    public function saveStep3()
    {
        return redirect()->route('admin.inventory.items.edit', [$this->item->id,'step'=>4]);
    }

    /* step 4 – Partners */

    public function saveStep4()
    {
        try {
            $validated = $this->validate([
                'descriptions.en' => 'nullable|string',
                "descriptions.{$this->second_lang}" => 'nullable|string',
                'short_descriptions.en' => 'nullable|string',
                "short_descriptions.{$this->second_lang}" => 'nullable|string',
                'seo_title.en' => 'nullable|string|max:255',
                "seo_title.{$this->second_lang}" => 'nullable|string|max:255',
                'seo_description.en' => 'nullable|string|max:1000',
                "seo_description.{$this->second_lang}" => 'nullable|string|max:1000',
                'seo_keywords.en' => 'nullable|string|max:1000',
                "seo_keywords.{$this->second_lang}" => 'nullable|string|max:1000',
            ]);

            $this->item->update([
                'description' => [
                    'en' => $this->descriptions['en'] ?? null,
                    $this->second_lang => $this->descriptions[$this->second_lang] ?? null,
                ],
                'short_description' => [
                    'en' => $this->short_descriptions['en'] ?? null,
                    $this->second_lang => $this->short_descriptions[$this->second_lang] ?? null,
                ],
            ]);

            $locales = array_values(array_unique(['en', $this->second_lang]));
            foreach ($locales as $locale) {
                $seoPairs = [
                    "seo_title_{$locale}" => $this->seo_title[$locale] ?? '',
                    "seo_description_{$locale}" => $this->seo_description[$locale] ?? '',
                    "seo_keywords_{$locale}" => $this->seo_keywords[$locale] ?? '',
                ];

                foreach ($seoPairs as $fieldName => $value) {
                    $value = is_string($value) ? trim($value) : '';
                    $customField = CustomField::firstOrCreate(
                        [
                            'module' => 'inventory',
                            'model' => 'item',
                            'name' => $fieldName,
                        ],
                        [
                            'type' => 'text',
                            'options' => null,
                            'is_required' => false,
                            'show_in_list' => false,
                        ]
                    );

                    ItemCustomValue::updateOrCreate(
                        [
                            'custom_field_id' => $customField->id,
                            'item_id' => $this->item->id,
                        ],
                        [
                            'value' => $value !== '' ? $value : null,
                        ]
                    );
                }
            }

            session()->flash('message', 'Description & SEO saved.');

            return redirect()->route('admin.inventory.items.show', $this->item->id);
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to save Description & SEO: '.$e->getMessage());
            \Log::error('Error in saveStep4 (Edit.php): '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());
        }
    }

    /* step 5 – Related Docs */

    public function saveStep5()
    {
        return redirect()->route('admin.inventory.items.show', ['item' => $this->item->id]);
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

        // Update categories list and reset new category name
        $this->newCategoryName = '';
        $this->newCategoryParentId = null;
    }

    public function addPartner()
    {
        $this->business_partners[] = '';
        $this->partner_percentages[] = 0;
    }

    public function openDocModal()
    {
        $this->showDocModal = true;
    }

    public function closeDocModal()
    {
        $this->showDocModal = false;
    }

    public function removePartner($index)
    {
        unset($this->business_partners[$index]);
        unset($this->partner_percentages[$index]);
        $this->business_partners = array_values($this->business_partners);
        $this->partner_percentages = array_values($this->partner_percentages);
    }

    public function goToStep($step)
    {
        $this->step = $step;
        if ((int) $step === 4) {
            $this->dispatch('inventory-item-step4-opened');
        }
    }

    #[\Livewire\Attributes\On('variantsSaved')]
    public function onVariantsSaved(): void
    {
        $this->step = 3;
    }

    // MediaManager Events
    #[\Livewire\Attributes\On('media-uploaded')]
    public function onMediaUploaded($entityId): void
    {
        session()->flash('message', 'Images uploaded successfully!');
    }

    #[\Livewire\Attributes\On('media-deleted')]
    public function onMediaDeleted($id): void
    {
        session()->flash('message', 'Image deleted successfully!');
    }

    #[\Livewire\Attributes\On('media-primary-changed')]
    public function onMediaPrimaryChanged($id): void
    {
        session()->flash('message', 'Primary image changed!');
    }

    public function render()
    {
        $categories = ItemCategory::all();
        $parent_categories = ItemCategory::with('children')->whereNull('parent_id')->orWhere('parent_id', 0)->get();
        $sponsors = Sponsor::all();
        $businessPartners = BusinessPartner::all();
        $brands = Brand::all();

        $currencies = Currency::query()->orderBy('code')->get(['code', 'name']);

        return view('inventory::livewire.items.edit', compact('categories', 'parent_categories', 'businessPartners', 'sponsors', 'brands', 'currencies'));
    }
}
