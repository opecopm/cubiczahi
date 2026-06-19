<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Update Item',
        'breadcrumbs' => [
            [
                'label' => 'Items',
                'url' => route('admin.inventory.items.index'),
                'icon' => 'back',
            ],
            [
                'label' => 'Edit',
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
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="#tabs-basic-info"
                                        class="nav-link @if ($step == 1) active @endif"
                                        wire:click.prevent="goToStep(1)">1. Basic Info</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tabs-price-info"
                                        class="nav-link @if ($step == 2) active @endif"
                                        wire:click.prevent="goToStep(2)">2. Variations</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tabs-images"
                                        class="nav-link @if ($step == 3) active @endif"
                                        wire:click.prevent="goToStep(3)">3. Images</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tabs-description-seo"
                                        class="nav-link @if ($step == 4) active @endif"
                                        wire:click.prevent="goToStep(4)">4. Description & SEO</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane @if ($step == 1) active show @endif"
                                    id="tabs-basic-info">
                                    @if ($step == 1)
                                        @include('inventory::livewire.items.partials.form-step1')
                                    @endif
                                </div>
                                <div class="tab-pane @if ($step == 2) active show @endif"
                                    id="tabs-price-info">
                                    @if ($step == 2)
                                        @include('inventory::livewire.items.partials.form-step2')
                                    @endif
                                </div>
                                <div class="tab-pane @if ($step == 3) active show @endif"
                                    id="tabs-images">
                                    @if ($step == 3)
                                        @include('inventory::livewire.items.partials.form-step3-images')
                                    @endif
                                </div>
                                <div class="tab-pane @if ($step == 4) active show @endif"
                                    id="tabs-description-seo">
                                    @if ($step == 4)
                                        @include('inventory::livewire.items.partials.form-step4')
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Brand Modal -->
            <div class="modal modal-blur fade" id="addBrandModal" tabindex="-1" role="dialog"
                aria-labelledby="addBrandModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="brandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brandName" wire:model="newBrandName">
                            @error('newBrandName')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                wire:click="addBrand">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Category Modal -->
            <div class="modal modal-blur fade" id="addCategoryModal" tabindex="-1" role="dialog"
                aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" wire:model="newCategoryName">
                            @error('newCategoryName')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @php
                                if (!function_exists('renderCategoryOptionsCreate')) {
                                    function renderCategoryOptionsCreate($categories, $prefix = '')
                                    {
                                        foreach ($categories as $cat) {
                                            echo '<option value="' .
                                                $cat->id .
                                                '">' .
                                                $prefix .
                                                $cat->name .
                                                '</option>';
                                            if ($cat->children && $cat->children->count()) {
                                                renderCategoryOptionsCreate($cat->children, $prefix . '— ');
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="mt-3">
                                <label for="parentCategory" class="form-label">Parent Category</label>
                                <select class="form-select" id="parentCategory" wire:model="newCategoryParentId">
                                    <option value="">None</option>
                                    @php renderCategoryOptionsCreate($parent_categories ?? []); @endphp
                                </select>
                                @error('newCategoryParentId')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                wire:click="addCategory">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Generate Modal -->
            <div class="modal modal-blur fade" id="aiGenerateModal" tabindex="-1" role="dialog" wire:ignore.self
                aria-labelledby="aiGenerateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="aiGenerateModalLabel">
                                <i class="ti ti-sparkles text-warning me-2"></i> Generate with AI
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="aiPrompt" class="form-label">What do you want to generate?</label>
                            <textarea class="form-control" id="aiPrompt" wire:model.defer="aiPrompt" rows="4" placeholder="e.g. Generate a description for an iPhone 14 Pro Max 256GB..."></textarea>
                            @error('aiPrompt')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="mt-3 text-secondary" style="font-size: 0.85rem;">
                                This will generate and overwrite the English <strong>Description</strong> and <strong>Short Description</strong> fields.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary"
                                wire:click="generateEnglishContent" wire:loading.attr="disabled" wire:target="generateEnglishContent">
                                <span wire:loading wire:target="generateEnglishContent" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                <i class="ti ti-wand me-1" wire:loading.remove wire:target="generateEnglishContent"></i>
                                Generate
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('js')
    <script>
        (function() {
            if (window.__inventoryItemStep4TinyMceBooted) {
                return;
            }

            window.__inventoryItemStep4TinyMceBooted = true;

            var configs = [{
                    id: 'tiny-description-en',
                    inputId: 'wire-description-en',
                    toolbar: 'blocks | bold italic underline strikethrough | bullist numlist | blockquote link | removeformat',
                    height: 240
                },
                {
                    id: 'tiny-short-en',
                    inputId: 'wire-short-en',
                    toolbar: 'bold italic underline | bullist numlist | link | removeformat',
                    height: 140
                }
            ];

            @foreach ($active_languages as $lang)
            @if ($lang !== 'en')
                configs.push({
                    id: 'tiny-description-{{ $lang }}',
                    inputId: 'wire-description-{{ $lang }}',
                    toolbar: 'blocks | bold italic underline strikethrough | bullist numlist | blockquote link | removeformat',
                    height: 240
                });
                configs.push({
                    id: 'tiny-short-{{ $lang }}',
                    inputId: 'wire-short-{{ $lang }}',
                    toolbar: 'bold italic underline | bullist numlist | link | removeformat',
                    height: 140
                });
            @endif
            @endforeach

            function syncToWireInput(cfg, content) {
                var input = document.getElementById(cfg.inputId);
                if (!input) {
                    return;
                }

                input.value = content;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            function destroyIfMissing(id) {
                if (typeof tinymce === 'undefined') {
                    return;
                }

                var editor = tinymce.get(id);
                if (!editor) {
                    return;
                }

                if (!document.getElementById(id)) {
                    editor.remove();
                }
            }

            function initEditors() {
                if (typeof tinymce === 'undefined') {
                    return;
                }

                configs.forEach(function(cfg) {
                    var textarea = document.getElementById(cfg.id);
                    var existing = tinymce.get(cfg.id);

                    if (!textarea) {
                        if (existing) {
                            existing.remove();
                        }
                        return;
                    }

                    if (existing) {
                        return;
                    }

                    tinymce.init({
                        selector: '#' + cfg.id,
                        menubar: false,
                        plugins: 'lists link',
                        toolbar: cfg.toolbar,
                        min_height: cfg.height,
                        setup: function(editor) {
                            editor.on('init', function() {
                                syncToWireInput(cfg, editor.getContent());
                            });
                            editor.on('change input keyup', function() {
                                syncToWireInput(cfg, editor.getContent());
                            });
                        }
                    });
                });
            }

            function scheduleInit() {
                if (window.__inventoryItemStep4TinyMceTimer) {
                    clearTimeout(window.__inventoryItemStep4TinyMceTimer);
                }

                window.__inventoryItemStep4TinyMceTimer = setTimeout(function() {
                    configs.forEach(function(cfg) {
                        destroyIfMissing(cfg.id);
                    });
                    initEditors();
                }, 50);
            }

            function attachHooks() {
                if (!window.Livewire || typeof Livewire.hook !== 'function') {
                    return;
                }

                Livewire.hook('morph.added', scheduleInit);
                Livewire.hook('morph.updated', scheduleInit);
            }

            attachHooks();
            document.addEventListener('livewire:initialized', function () {
                attachHooks();
                Livewire.on('close-ai-modal', function () {
                    var modalEl = document.getElementById('aiGenerateModal');
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                        
                        // Fix for modal backdrop sometimes getting stuck
                        var backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('overflow');
                        document.body.style.removeProperty('padding-right');
                    }
                });
                Livewire.on('tinymce-content-updated', function (data) {
                    var item = data[0];
                    if (typeof tinymce !== 'undefined') {
                        var editor = tinymce.get(item.id);
                        if (editor) {
                            editor.setContent(item.content);
                        }
                    }
                });
            });
            document.addEventListener('shown.bs.collapse', scheduleInit);
            document.addEventListener('inventory-item-step4-opened', scheduleInit);
            scheduleInit();
        })();
    </script>
@endpush
