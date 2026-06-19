<form wire:submit.prevent="saveStep1">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 align-items-start">

        {{-- ==================== MAIN CONTENT ==================== --}}
        <div class="col-md-8">

            {{-- Names --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Names</h4>
                    <button type="button" class="btn btn-sm btn-outline-info" wire:click="autoTranslate" wire:loading.attr="disabled">
                        <span wire:loading wire:target="autoTranslate" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <i class="ti ti-language me-1" wire:loading.remove wire:target="autoTranslate"></i>
                        Auto-Translate Empty
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name_en" class="form-label">Item Name (English)</label>
                            <input type="text" class="form-control" id="name_en" wire:model.live="name.en">
                            @error('name.en')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        @foreach($active_languages as $lang)
                        <div class="col-md-6">
                            <label for="name_{{ $lang }}" class="form-label">Item Name ({{ strtoupper($lang) }})</label>
                            <input type="text" class="form-control" id="name_{{ $lang }}" wire:model.live="name.{{ $lang }}">
                            @error('name.' . $lang)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Slug --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title">Slug</h4>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text text-secondary">/items/</span>
                        <input type="text" class="form-control" id="slug" wire:model.live="slug"
                               placeholder="auto-generated-from-name">
                        <button type="button" class="btn btn-outline-secondary" title="Re-generate from name"
                                wire:click="regenerateSlug">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                    <div class="form-hint">URL-friendly identifier. Auto-fills from English name; edit to override.</div>
                    @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title">Prices</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sell_price" class="form-label">Sell Price</label>
                            <input type="number" step="0.01" class="form-control" id="sell_price" wire:model="sell_price">
                            @error('sell_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_price" wire:model="purchase_price">
                            @error('purchase_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="currency_code" class="form-label">Currency</label>
                            <select id="currency_code" class="form-select" wire:model="currency_code">
                                @if(isset($currencies) && $currencies->count())
                                    @foreach($currencies as $cur)
                                        <option value="{{ $cur->code }}">{{ $cur->code }}{{ !empty($cur->name) ? ' — '.$cur->name : '' }}</option>
                                    @endforeach
                                @else
                                    <option value="{{ $currency_code }}">{{ $currency_code }}</option>
                                @endif
                            </select>
                            @error('currency_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /col-md-8 --}}

        {{-- ==================== RIGHT SIDEBAR ==================== --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Settings</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="type" class="form-label">Type</label>
                            <select id="type" wire:model.live="type" class="form-select">
                                <option value="">Select Type</option>
                                @foreach (\Modules\Inventory\Models\Item::TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="reference" class="form-label">Reference / Code</label>
                            <input type="text" class="form-control" id="reference" wire:model.live="reference"
                                   placeholder="Auto-generated if left empty">
                            @error('reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <livewire:entity-select
                                entity="item_category"
                                label="Category"
                                icon="category"
                                :addNewUrl="route('admin.inventory.item-categories.index')"
                                wire:model.live="category_id"
                                :value="$category_id"
                                :key="'item-create-category-'.$step"
                            />
                            @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <livewire:entity-select
                                entity="brand"
                                label="Brand"
                                icon="award"
                                :addNewUrl="route('admin.inventory.brands.index')"
                                wire:model.live="brand_id"
                                :value="$brand_id"
                                :key="'item-create-brand-'.$step"
                            />
                            @error('brand_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="icon_class" class="form-label">Icon Class</label>
                            <div class="input-group">
                                <span class="input-group-text" id="icon-preview" style="width:2.5rem;justify-content:center;">
                                    @if($icon_class)
                                        <i class="{{ $icon_class }}"></i>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-secondary" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7"/><circle cx="12" cy="12" r="4"/></svg>
                                    @endif
                                </span>
                                <input type="text" class="form-control" id="icon_class" wire:model.live="icon_class"
                                       placeholder="e.g. ti ti-shirt">
                            </div>
                            <div class="form-hint">Tabler icon class, e.g. <code>ti ti-droplet</code></div>
                            @error('icon_class')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" wire:model="status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12"><hr class="my-1"></div>

                        <div class="col-12 d-flex align-items-center gap-3 flex-wrap">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="track_inventory" wire:model="track_inventory">
                                <label class="form-check-label" for="track_inventory">Track Inventory</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_serialized" wire:model="is_serialized">
                                <label class="form-check-label" for="is_serialized">Serialized</label>
                            </div>
                            @if (in_array($type, ['product', 'service']))
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="has_variants" wire:model.live="has_variants">
                                    <label class="form-check-label" for="has_variants">Has Variants</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /col-md-4 --}}

    </div>{{-- /row --}}

    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"
                wire:loading.attr="disabled"
                wire:target="saveStep1">
            <span wire:loading wire:target="saveStep1"
                  class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Save &amp; Next
        </button>
    </div>
</form>
