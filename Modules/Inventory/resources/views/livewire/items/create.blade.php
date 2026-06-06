<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Add New ' . ($type ?? 'Item'),
        'breadcrumbs' => [
            [
                'label' => 'Items',
                'url' => route('admin.inventory.items.index'),
                'icon' => 'back',
            ],
            [
                'label' => 'Create',
                'active' => true,
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                   <h4 class="card-title">Basic Info</h4>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="tabs-basic-info">
                            @include('inventory::livewire.items.partials.form-step1')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal modal-blur fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="brandName" class="form-label">Brand Name</label>
                    <input type="text" class="form-control" id="brandName" wire:model="newBrandName">
                    @error('newBrandName')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" wire:click="addBrand">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal modal-blur fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="categoryName" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="categoryName" wire:model="newCategoryName">
                    @error('newCategoryName')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @php
                        function renderCategoryOptionsCreate($categories, $prefix = '') {
                            foreach ($categories as $cat) {
                                echo '<option value="'.$cat->id.'">'.$prefix.$cat->name.'</option>';
                                if ($cat->children && $cat->children->count()) {
                                    renderCategoryOptionsCreate($cat->children, $prefix.'— ');
                                }
                            }
                        }
                    @endphp
                    <div class="mt-3">
                        <label for="parentCategory" class="form-label">Parent Category</label>
                        <select class="form-select" id="parentCategory" wire:model="newCategoryParentId">
                            <option value="">None</option>
                            @php renderCategoryOptionsCreate($parent_categories); @endphp
                        </select>
                        @error('newCategoryParentId')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" wire:click="addCategory">Save</button>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>
</div>
