<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Add New Project</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <form wire:submit.prevent="create" enctype="multipart/form-data">
                <div class="row g-3">

                    <!-- Left Column: Content -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-body">

                                <!-- Language Tabs -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($languages as $locale => $label)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if($locale === $defaultLocale) active @endif"
                                                id="tab-{{ $locale }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#content-{{ $locale }}"
                                                type="button"
                                                role="tab">
                                                {{ $label }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content mb-4">
                                    @foreach($languages as $locale => $label)
                                        <div class="tab-pane fade @if($locale === $defaultLocale) show active @endif"
                                            id="content-{{ $locale }}" role="tabpanel">

                                            <div class="mb-3">
                                                <label class="form-label">Project Title</label>
                                                <input type="text" wire:model.defer="project_title.{{ $locale }}"
                                                    class="form-control"
                                                    placeholder="Enter project title">
                                                @error("project_title.$locale")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Short Description</label>
                                                <textarea wire:model.defer="short_description.{{ $locale }}"
                                                    class="form-control" rows="3"
                                                    placeholder="Brief summary"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Full Description</label>
                                                <textarea wire:model.defer="project_description.{{ $locale }}"
                                                    class="form-control" rows="8"
                                                    placeholder="Detailed project description"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>

                        <!-- Project Details -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Project Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Categories</label>
                                        <div class="border rounded p-3 mb-2" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($categories as $parent)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="{{ $parent->category_id }}" wire:model="selectedCategories" id="cat-{{ $parent->category_id }}">
                                                    <label class="form-check-label fw-bold" for="cat-{{ $parent->category_id }}">
                                                        {{ is_array($parent->category_name) ? ($parent->category_name[$defaultLocale] ?? '') : $parent->category_name }}
                                                    </label>
                                                </div>
                                                @foreach($parent->children as $child)
                                                    <div class="form-check ms-3">
                                                        <input class="form-check-input" type="checkbox" value="{{ $child->category_id }}" wire:model="selectedCategories" id="cat-{{ $child->category_id }}">
                                                        <label class="form-check-label" for="cat-{{ $child->category_id }}">
                                                            — {{ is_array($child->category_name) ? ($child->category_name[$defaultLocale] ?? '') : $child->category_name }}
                                                        </label>
                                                    </div>
                                                    @foreach($child->children as $grandchild)
                                                        <div class="form-check ms-5">
                                                            <input class="form-check-input" type="checkbox" value="{{ $grandchild->category_id }}" wire:model="selectedCategories" id="cat-{{ $grandchild->category_id }}">
                                                            <label class="form-check-label small text-muted" for="cat-{{ $grandchild->category_id }}">
                                                                —— {{ is_array($grandchild->category_name) ? ($grandchild->category_name[$defaultLocale] ?? '') : $grandchild->category_name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
                                            <i class="ti ti-plus me-1"></i> Add New Category
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tags (comma separated)</label>
                                        <input type="text" wire:model.defer="tags" class="form-control" placeholder="e.g. Design, Development">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3" wire:ignore>
                                        <label class="form-label">Start Date</label>
                                        <input type="text" id="start_date" class="form-control" placeholder="DD - MM - YYYY" wire:model.defer="start_date">
                                    </div>
                                    <div class="col-md-6 mb-3" wire:ignore>
                                        <label class="form-label">End Date</label>
                                        <input type="text" id="end_date" class="form-control" placeholder="DD - MM - YYYY" wire:model.defer="end_date">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Icon Class (FontAwesome/Bootstrap)</label>
                                    <input type="text" wire:model.defer="icon_class" class="form-control" placeholder="e.g., fa fa-building">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Additional Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    @foreach($languages as $locale => $label)
                                        <div class="tab-pane fade @if($locale === $defaultLocale) show active @endif" id="info-{{ $locale }}">
                                            <label class="form-label">Additional Info ({{ $label }})</label>
                                            <textarea wire:model.defer="additional_info.{{ $locale }}" class="form-control" rows="3" placeholder="Any extra details..."></textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Settings & Media -->
                    <div class="col-lg-4">

                        <div class="card mb-3">
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-device-floppy me-1"></i> Publish Project
                                </button>
                            </div>
                        </div>

                        <!-- Publish Settings -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Publish Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select wire:model.defer="status" class="form-control">
                                        <option value="upcoming">Upcoming</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isActive" wire:model.defer="is_active">
                                        <span class="form-check-label">Active Project</span>
                                    </label>
                                </div>

                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isUpcoming" wire:model.defer="is_upcoming">
                                        <span class="form-check-label">Mark as Upcoming</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Media Uploads -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Media</h3>
                            </div>
                            <div class="card-body">

                                <!-- Main Image -->
                                <div class="mb-3" x-data="{ mainPreview: null }">
                                    <label class="form-label">Main Image</label>
                                    <input type="file" wire:model="main_image" class="form-control mb-2" accept="image/*"
                                        @change="mainPreview = URL.createObjectURL($event.target.files[0])">
                                    <template x-if="mainPreview">
                                        <img :src="mainPreview" class="img-fluid rounded mt-2" style="max-height: 150px;">
                                    </template>
                                    @error('main_image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Icon Image -->
                                <div class="mb-3" x-data="{ iconPreview: null }">
                                    <label class="form-label">Icon Image (Optional)</label>
                                    <input type="file" wire:model="icon_image" class="form-control mb-2" accept="image/*"
                                        @change="iconPreview = URL.createObjectURL($event.target.files[0])">
                                    <template x-if="iconPreview">
                                        <img :src="iconPreview" class="img-fluid rounded mt-2" style="max-height: 50px;">
                                    </template>
                                </div>

                                <!-- Gallery -->
                                <div class="mb-3">
                                    <label class="form-label">Gallery Images</label>
                                    <input type="file" wire:model="gallery_images" multiple class="form-control mb-2" accept="image/*">
                                    @if($gallery_images)
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach($gallery_images as $img)
                                                <img src="{{ $img->temporaryUrl() }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- New Category Modal -->
    <div class="modal fade" id="newCategoryModal" tabindex="-1" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select wire:model.defer="newCategoryParentId" class="form-control">
                            <option value="">No Parent (Root Category)</option>
                            @foreach($flattenedCategories as $cat)
                                <option value="{{ $cat->category_id }}">
                                    {{ is_array($cat->category_name) ? ($cat->category_name[$defaultLocale] ?? '') : $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        @foreach($languages as $locale => $label)
                            <li class="nav-item">
                                <button class="nav-link @if($locale === $defaultLocale) active @endif" data-bs-toggle="tab" data-bs-target="#cat-{{ $locale }}" type="button">{{ $label }}</button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @foreach($languages as $locale => $label)
                            <div class="tab-pane fade @if($locale === $defaultLocale) show active @endif" id="cat-{{ $locale }}">
                                <div class="mb-3">
                                    <label class="form-label">Category Name ({{ $label }})</label>
                                    <input type="text" wire:model.defer="newCategoryName.{{ $locale }}" class="form-control">
                                    @error("newCategoryName.$locale") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" wire:click="createCategory" class="btn btn-primary">Save Category</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('close-category-modal', event => {
            const modalEl = document.getElementById('newCategoryModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
            }
        });
    </script>
    <script>
        document.addEventListener('livewire:initialized', () => {
             flatpickr("#start_date", {
                dateFormat: "d - m - Y",
                onChange: function(selectedDates, dateStr, instance) {
                    @this.set('start_date', dateStr);
                }
            });
            flatpickr("#end_date", {
                dateFormat: "d - m - Y",
                onChange: function(selectedDates, dateStr, instance) {
                    @this.set('end_date', dateStr);
                }
            });
        });
    </script>
</div>
