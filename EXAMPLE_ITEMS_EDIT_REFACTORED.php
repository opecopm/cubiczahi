<?php

namespace Modules\Inventory\Livewire\Items;

use App\Livewire\WithAutoComplete;
use App\Livewire\WithModalTrait;
use App\Traits\HasMediaManagement;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Assets\Models\Asset;
use Modules\Assets\Models\AssetCategory;
use Modules\Assets\Models\AssetDocument;
use Modules\Assets\Models\AssetDocumentType;
use Modules\Assets\Models\AssetPhoto;
use Modules\Business\Models\BusinessPartner;
use Modules\Business\Models\Currency;
use Modules\Business\Models\Department;
use Modules\Business\Models\Location;
use Modules\Business\Models\Sponsor;
use Modules\HRM\Models\Employee;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemImage;
use Modules\Inventory\Models\ItemPrice;
use Modules\Inventory\Models\ItemVariant;
use Modules\Inventory\Models\VariantImage;
use Modules\Purchasing\Models\Vendor;

use function system_setting;

/**
 * REFACTORED VERSION - Using MediaManager Component
 *
 * Key Changes:
 * 1. Added HasMediaManagement trait
 * 2. Removed image-related properties (newItemImages, newVariantImages, itemImages, variantImagesList, etc.)
 * 3. Removed image upload/delete methods (uploadItemImages, deleteItemImage, etc.)
 * 4. All media management is now handled by the MediaManager component
 * 5. Updated loadItemImages() and loadVariantImages() calls
 *
 * Benefits:
 * - Cleaner component code
 * - Reusable media handling
 * - Less code to maintain
 * - Consistent UI/UX across the app
 */
class EditRefactored extends Component
{
    use WithAutoComplete, WithFileUploads, WithModalTrait, HasMediaManagement;

    public $second_lang;
    public $type;
    public $name = [];
    public $asset_name = [];
    public $name_en;
    public $description;
    public $category_id;
    public $brand_id;
    public $is_asset = false;
    public $track_inventory = false;
    public $is_serialized = false;
    public $has_variants = false;
    public $asset_description;
    public $purchase_date;
    public $purchase_cost;
    public $value;
    public $status;
    public $sponsor_id;
    public $model_number;
    public $model_year;
    public $serial_number;
    public $short_description;
    public $asset_category_id;
    public $department_id;
    public $location_id;
    public $employee_id;
    public $asset_vendor_id;
    public $purchase_order_number;
    public $invoice_number;
    public $warranty_end_date;
    public $next_maintenance_date;
    public $depreciation_method;
    public $useful_life_months;
    public $salvage_value;
    public $plate_number;
    public $warranty_months;
    public $newBrandName;
    public $newCategoryName;
    public $newCategoryParentId;
    public $sell_price;
    public $purchase_price;
    public string $currency_code = 'SAR';
    public int $step = 1;
    public $item;
    public $asset;
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
    public $asset_doc_types = [];
    public $customer_id;
    public $vendor_id;
    public $vendor_email;
    public $vendorSuggestions = [];
    public $vendorSuggestionsList = 'hidden';
    public $date_from;
    public $date_to;
    public $showDocModal = false;

    // NOTE: Removed these - now handled by MediaManager:
    // public $itemImages = [];
    // public $newItemImages = [];
    // public $variantImagesList = [];
    // public $newVariantImages = [];

    public $customFields = [];
    public $customValues = [];

    protected $queryString = ['step' => ['except' => 1]];

    public function mount($itemId)
    {
        $this->customFields = (new Asset)->modelCustomFields();
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
        $this->is_asset = $item->is_asset;
        $this->track_inventory = (bool) $item->track_inventory;
        $this->is_serialized = (bool) $item->is_serialized;
        $this->has_variants = (bool) $item->has_variants;

        // Initialize MediaManager for item images
        // This replaces the old loadItemImages() logic
        $this->initializeMediaManager('item', $this->item->id, 'image', [
            'title' => 'Product Images',
            'description' => 'Upload product images for the gallery',
            'allowMultiple' => true,
            'allowPrimary' => true,
            'acceptedFormats' => 'image/*',
        ]);

        $this->warranty_months = $item->warranty_months;
        $this->sell_price = $item->price('sell')->price ?? 0;
        $this->purchase_price = 0;

        if ($item->price('purchase')) {
            $this->purchase_price = $item->price('purchase')->price ?? 0;
            $this->vendor_id = @$item->price('purchase')->vendor_id ?? null;
            if (@$this->vendor_id) {
                $vendor = Vendor::find($this->vendor_id);
                $this->vendor_email = $vendor->email;
            }
        }

        if ($this->is_asset && $asset = $item->asset) {
            $this->asset_name['en'] = $asset->name ?? $item->getTranslation('name', 'en');
            $this->asset_name[$this->second_lang] = $asset->getTranslation('name', $this->second_lang);
            $this->asset_description = $asset->description == null ? $item->description : '';
            $this->purchase_date = $asset->purchase_date;
            $this->purchase_cost = $asset->purchase_cost;
            $this->value = $asset->value;
            $this->status = $asset->status;
            $this->sponsor_id = $asset->sponsor_id;
            $this->model_number = $asset->model_number;
            $this->model_year = $asset->model_year;
            $this->serial_number = $asset->serial_number;
            $this->short_description = $asset->short_description;
            $this->asset_category_id = $asset->asset_category_id;
            $this->department_id = $asset->department_id;
            $this->location_id = $asset->location_id;
            $this->employee_id = $asset->employee_id;
            $this->asset_vendor_id = $asset->vendor_id;
            $this->purchase_order_number = $asset->purchase_order_number;
            $this->invoice_number = $asset->invoice_number;
            $this->warranty_end_date = $asset->warranty_end_date;
            $this->next_maintenance_date = $asset->next_maintenance_date;
            $this->depreciation_method = $asset->depreciation_method;
            $this->useful_life_months = $asset->useful_life_months;
            $this->salvage_value = $asset->salvage_value;
            $this->plate_number = $asset->plate_number;

            foreach ($asset->businessPartners as $partner) {
                $this->business_partners[] = $partner->id;
                $this->partner_percentages[] = $partner->pivot->percentage;
            }

            $this->asset = $asset;
            $this->customFields = (new Asset)->modelCustomFields();

            foreach ($asset->customValues as $customValue) {
                $this->customValues[$customValue->custom_field_id] = $customValue->value;
            }

            $this->asset_doc_types = AssetDocumentType::all();
            $this->documents = AssetDocument::with('media')->where('asset_id', $this->asset->id)->get();
            $this->loadDocuments();
            $this->loadPhotos();
        }
    }

    public function loadDocuments()
    {
        $this->documents = AssetDocument::with('media')->where('asset_id', $this->asset->id)->get();
    }

    public function loadPhotos()
    {
        $this->photos = $this->asset->photos;
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
            'is_asset' => 'boolean',
            'track_inventory' => 'boolean',
            'is_serialized' => 'boolean',
            'sponsor_id' => 'nullable|exists:sponsors,id',
            'purchase_cost' => 'nullable',
            'status' => 'required|string',
            'primary_photo' => 'nullable|image|max:1024',
            'asset_name.en' => 'nullable|string',
            "asset_name.{$this->second_lang}" => 'nullable|string|min:3',
            'asset_description' => 'nullable|string',
            'value' => 'required_if:is_asset,true|min:0',
            'model_number' => 'nullable|string',
            'model_year' => 'nullable|string',
            'serial_number' => 'nullable|string',
        ];
    }

    public function updatedName($value, $key)
    {
        if ($key) {
            $this->asset_name[$key] = $value;
        }
    }

    public function saveStep1()
    {
        try {
            $this->validate($this->rules1());

            $this->item->update([
                'type' => $this->type,
                'name' => [
                    'en' => $this->name['en'],
                    $this->second_lang => $this->name[$this->second_lang] ?? $this->name['en'],
                ],
                'description' => $this->asset_description,
                'category_id' => $this->category_id,
                'brand_id' => $this->brand_id,
                'is_asset' => $this->is_asset,
                'track_inventory' => (bool) $this->track_inventory,
                'is_serialized' => (bool) $this->is_serialized,
                'has_variants' => (bool) $this->has_variants,
                'warranty_months' => $this->warranty_months,
                'status' => $this->status,
            ]);

            if ($this->primary_photo) {
                $this->item->clearMediaCollection('primary_photo');
                $this->item->addMedia($this->primary_photo->getRealPath())->toMediaCollection('primary_photo');
            }

            if ($this->is_asset) {
                $this->asset = Asset::updateOrCreate(
                    ['item_id' => $this->item->id],
                    [
                        'name' => [
                            'en' => $asset->name ?? $this->asset_name['en'] ?? $this->name['en'],
                            $this->second_lang => $this->asset_name[$this->second_lang] ?? $this->name[$this->second_lang] ?? '',
                        ],
                        'description' => $this->asset_description,
                        'purchase_date' => $this->purchase_date,
                        'purchase_cost' => $this->purchase_cost,
                        'value' => $this->value ?? 0,
                        'status' => $this->status,
                        'sponsor_id' => $this->sponsor_id,
                        'model_number' => $this->model_number,
                        'model_year' => $this->model_year,
                        'serial_number' => $this->serial_number,
                    ]
                );

                foreach ($this->customValues as $fieldId => $value) {
                    $this->asset->customValues()->updateOrCreate(
                        ['custom_field_id' => $fieldId],
                        ['value' => $value]
                    );
                }
            }

            session()->flash('message', 'Item saved successfully in previous step.');
            $this->step = 2;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: '.$e->getMessage());
            \Log::error('Error in saveStep1: '.$e->getMessage().' on line '.$e->getLine());
        }
    }

    protected $rules2 = [
        'sell_price' => 'required|numeric|min:0',
        'purchase_price' => 'required|numeric|min:0',
        'currency_code' => 'required|string|size:3',
        'vendor_email' => 'nullable|email',
        'vendor_id' => 'nullable|integer',
        'customer_id' => 'nullable|integer',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
    ];

    public function updatedVendorEmail($value)
    {
        $value = trim((string) $value);
        if ($value == '') {
            $this->vendorSuggestions = [];
            $this->vendorSuggestionsList = 'hidden';
            $this->vendor_id = null;
            return;
        }

        $this->vendorSuggestions = $this->getSuggestions(Vendor::class, $value, ['email', 'name', 'phone']);
        if ($this->vendorSuggestions) {
            $this->vendorSuggestionsList = 'show';
        }
    }

    public function selectVendor($email)
    {
        $this->vendorSuggestionsList = 'hidden';
        $vendor = Vendor::where('email', $email)->first();
        if ($vendor) {
            $this->vendor_email = $vendor->email;
            $this->vendor_id = $vendor->id;
        }
    }

    protected function resolveVendorFromEmail(): void
    {
        if (!$this->vendor_id) {
            if ($this->vendor_email) {
                $vendor = Vendor::where('email', $this->vendor_email)->first();
                if ($vendor) {
                    $this->vendor_id = $vendor->id;
                }
            }
        }
    }

    public function saveStep2()
    {
        $this->validate($this->rules2);
        $this->resolveVendorFromEmail();

        $currency = Currency::where('code', $this->currency_code)->first();
        $rate = $currency->rate ?? 1.0;

        $from = $this->date_from ?: null;
        $to = $this->date_to ?: null;

        // SELLING PRICE
        if ($this->sell_price !== null) {
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
        }

        // PURCHASE PRICE
        if ($this->purchase_price !== null) {
            ItemPrice::updateOrCreate(
                [
                    'item_id' => $this->item->id,
                    'price_type' => 'purchase',
                    'vendor_id' => $this->vendor_id,
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

    /**
     * STEP 3 - Images now handled by MediaManager component
     *
     * In the view, use:
     * @livewire('media-manager', [
     *     'entityId' => $item->id,
     *     'entityType' => 'item',
     *     'mediaType' => 'image',
     *     'title' => 'Product Images',
     * ])
     *
     * Old methods removed:
     * - loadItemImages() - now automatic
     * - uploadItemImages() - replaced by MediaManager
     * - deleteItemImage() - replaced by MediaManager
     * - setPrimaryItemImage() - replaced by MediaManager
     * - loadVariantImages() - now automatic
     * - uploadVariantImage() - replaced by MediaManager
     * - deleteVariantImage() - replaced by MediaManager
     */
    public function saveStep3(): void
    {
        $this->step = 4;
        session()->flash('message', 'Images saved.');
    }

    /* step 4 – Partners */
    public function saveStep4()
    {
        $this->asset->businessPartners()->detach();
        if ($this->business_partners) {
            foreach ($this->business_partners as $index => $partner_id) {
                if ($partner_id) {
                    $this->asset->businessPartners()->attach($partner_id, ['percentage' => $this->partner_percentages[$index]]);
                }
            }
        }
        $this->step = 5;
        session()->flash('message', 'Item saved successfully in previous step.');
    }

    /* step 5 – Related Docs */
    public function saveStep5()
    {
        return redirect()->route('inventory.items.show', ['item' => $this->item->id]);
    }

    public function addDocument()
    {
        $this->validate([
            'newDocumentName' => 'required|string|min:3',
            'newDocumentFile' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'newDocumentExpiryDate' => 'nullable|date',
        ]);

        $asset = $this->asset;

        $document = AssetDocument::create([
            'name' => $this->newDocumentName,
            'expiry_date' => $this->newDocumentExpiryDate,
            'asset_id' => $asset->id,
        ]);

        if ($this->newDocumentFile) {
            $document->addMedia($this->newDocumentFile->getRealPath())->toMediaCollection('asset_documents');
        }

        session()->flash('message', 'Document added successfully.');
        $this->loadDocuments();
        $this->newDocumentFile = '';
        $this->closeDocModal();
    }

    public function confirmDeleteDocument($deleteDocId)
    {
        $this->deleteDocId = $deleteDocId;
    }

    public function deleteDocument()
    {
        AssetDocument::find($this->deleteDocId)->delete();
        session()->flash('message', 'Document deleted successfully.');
        return redirect(request()->header('Referer'));
    }

    public function addPhotos()
    {
        $this->validate([
            'newPhotosFiles.*' => 'required|image|max:2048',
            'newPhotoTitle' => 'required|string|min:3',
        ]);

        $photo = AssetPhoto::create([
            'title' => $this->newPhotoTitle,
            'asset_id' => $this->asset->id,
        ]);

        foreach ($this->newPhotosFiles as $newPhotoFile) {
            $photo->addMedia($newPhotoFile->getRealPath())->toMediaCollection('asset_photos');
        }

        $this->loadPhotos();
        session()->flash('message', 'Photos added successfully.');
        $this->newPhotosFiles = [];
    }

    public function confirmDeletePhoto($deletePhotoId)
    {
        $this->deletePhotoId = $deletePhotoId;
    }

    public function deletePhoto()
    {
        AssetPhoto::find($this->deletePhotoId)->delete();
        session()->flash('message', 'Photo deleted successfully.');
        return redirect(request()->header('Referer'));
    }

    public function addBrand()
    {
        Brand::create(['name' => $this->newBrandName]);
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
    }

    #[\Livewire\Attributes\On('variantsSaved')]
    public function onVariantsSaved(): void
    {
        $this->step = 3;
    }

    // Listen for MediaManager events
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
        $assetCategories = AssetCategory::all();
        $departments = Department::all();
        $locations = Location::all();
        $employees = Employee::select('id', 'first_name', 'last_name')->get();
        $vendors = Vendor::all();

        return view('inventory::livewire.items.edit', compact('categories', 'parent_categories', 'businessPartners', 'sponsors', 'brands', 'assetCategories', 'departments', 'locations', 'employees', 'vendors'));
    }
}
