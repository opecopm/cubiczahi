<!-- Add Item Modal -->
<div
    class="modal modal-blur fade @if($showModal) show d-block @endif"
    tabindex="-1"
    role="dialog"
    style="background: rgba(0, 0, 0, 0.5);"
    @if($showModal) aria-modal="true" @else aria-hidden="true" @endif
>
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editIndex !== null ? 'Edit Item' : 'Add New Item' }}</h5>
                <button type="button" class="btn-close" aria-label="Close" wire:click="closeModal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 position-relative">
                        <label for="item_reference" class="form-label">Item</label>
                        <input
                            type="text"
                            id="item_reference"
                            class="form-control"
                            wire:model.live="item_reference"
                            placeholder="Search Item Reference..."
                        >
                        @error('item_reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="suggestions-list {{ $itemSuggestionsList != 'show' ? 'd-none' : '' }}">
                            <ul class="list-group mt-2">
                                @forelse($itemSuggestions as $suggestion)
                                    <li class="list-group-item suggestion-item">
                                        <a href="javascript:void(0)" wire:click="selectItem('{{ $suggestion->reference }}')">
                                            {{ $suggestion->reference }} - {{ $suggestion->name }}
                                        </a>
                                    </li>
                                @empty
                                    <li class="list-group-item suggestion-item">No Data Found!</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" id="name" class="form-control" wire:model="name">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div
                            class="mb-1"
                            x-data="{
                                quill: null,
                                initEditor(initialHtml) {
                                    if (this.quill) return;
                                    this.quill = new Quill($refs.editor, { theme: 'snow' });
                                    this.quill.root.innerHTML = initialHtml || '';
                                    this.quill.on('text-change', () => {
                                        @this.set('description', this.quill.root.innerHTML);
                                    });
                                    window.addEventListener('init-description-quill', e => {
                                        if (this.quill) {
                                            this.quill.root.innerHTML = e.detail.html;
                                        }
                                    });
                                }
                            }"
                            x-init="initEditor(@js($description))"
                            wire:ignore
                        >
                            <label class="form-label">Description</label>
                            <div x-ref="editor" style="min-height: 150px;"></div>
                        </div>
                        @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" id="quantity" class="form-control" wire:model.live="quantity">
                        @error('quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="unit" class="form-label">Unit</label>
                        <input type="text" id="unit" class="form-control" wire:model="unit">
                        @error('unit')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="price" class="form-label">Unit Price</label>
                        <input type="number" id="price" class="form-control" wire:model.live="price">
                        @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="discount_type" class="form-label">Discount Type</label>
                        <select id="discount_type" class="form-select" wire:model.live="discount_type">
                            <option value="">Select</option>
                            <option value="fixed">Fixed</option>
                            <option value="percent">Percentage</option>
                        </select>
                        @error('discount_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="discount_rate" class="form-label">Discount Rate</label>
                        <input type="number" id="discount_rate" class="form-control" wire:model.live="discount_rate">
                        @error('discount_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="tax_id" class="form-label">Tax</label>
                        <select id="tax_id" class="form-select" wire:model="tax_id">
                            <option value="">Select</option>
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->rate }}%)</option>
                            @endforeach
                        </select>
                        @error('tax_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Cancel</button>
                <button type="button" class="btn btn-primary" wire:click="addItem">
                    {{ $editIndex !== null ? 'Update Item' : 'Add Item' }}
                </button>
            </div>
        </div>
    </div>
</div>
