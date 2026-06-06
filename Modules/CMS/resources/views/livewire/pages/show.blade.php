<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ $page->title }}</h2>
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                        <span class="badge bg-light text-dark border"><i class="ti ti-link me-1"></i> /{{ $page->slug }}</span>
                        <span class="badge bg-{{ $page->status === 'published' ? 'success' : 'secondary' }}-lt">
                            {{ ucfirst($page->status) }}
                        </span>
                        <span class="text-muted small">{{ $page->template_type == 'custom' ? $page->template_name : 'Default Template' }}</span>
                        @if ($page->published_at)
                            <span class="text-muted small">{{ \Carbon\Carbon::parse($page->published_at)->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a class="btn btn-outline-secondary me-2" href="{{ route('admin.cms.pages.edit', $page) }}">
                        <i class="ti ti-pencil me-1"></i> Edit Page
                    </a>
                    <button class="btn btn-primary" wire:click="createSection">
                        <i class="ti ti-plus me-1"></i> Add Section
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <h5 class="mb-0 fw-bold">Page Sections</h5>
                        <small class="text-muted">{{ $sections ? $sections->count() : 0 }} sections</small>
                    </div>

                    @if($sections && $sections->count() > 0)
                        <div class="accordion" id="sectionsAccordion">
                            @foreach($sections as $index => $section)
                                <div class="card mb-3 border-0 shadow-sm" wire:key="section-{{ $section->id }}" style="overflow: hidden;">
                                    <div class="card-header bg-white p-0 border-0" id="heading{{ $section->id }}">
                                        <div class="d-flex align-items-stretch">
                                            <div class="bg-light d-flex align-items-center justify-content-center px-2 text-muted border-end">
                                                <i class="ti ti-grip-vertical"></i>
                                            </div>

                                            <div class="p-3 flex-grow-1 d-flex align-items-center"
                                                 data-bs-toggle="collapse"
                                                 data-bs-target="#collapse{{ $section->id }}"
                                                 aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                 aria-controls="collapse{{ $section->id }}"
                                                 style="cursor: pointer;">
                                                <div class="me-3">
                                                    <div class="fw-bold {{ $section->is_enabled ? '' : 'text-decoration-line-through text-muted' }}">
                                                        {{ $section->title ?: 'Untitled Section' }}
                                                    </div>
                                                    @if($section->subtitle)
                                                        <small class="text-muted d-block">{{ Str::limit($section->subtitle, 60) }}</small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center pe-3 gap-2">
                                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2">
                                                    <i class="ti ti-layout-grid me-1"></i> {{ isset($blocks[$section->id]) ? $blocks[$section->id]->count() : 0 }}
                                                </span>
                                                <div class="vr h-50 my-auto text-muted"></div>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-ghost-secondary" wire:click.prevent="editSection({{ $section->id }})" title="Edit Settings">
                                                        <i class="ti ti-settings"></i>
                                                    </button>
                                                    <button class="btn btn-ghost-success" wire:click.prevent="createBlock({{ $section->id }})" title="Add Block">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                    <button class="btn btn-ghost-{{ $section->is_enabled ? 'warning' : 'secondary' }}" wire:click.prevent="toggleSectionStatus({{ $section->id }})" title="Toggle Visibility">
                                                        <i class="ti ti-{{ $section->is_enabled ? 'eye' : 'eye-off' }}"></i>
                                                    </button>
                                                    <button class="btn btn-ghost-danger" wire:click.prevent="deleteSection({{ $section->id }})"
                                                            wire:confirm="Delete this section?" title="Delete">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                                <button class="btn btn-ghost-secondary p-0 ms-2" data-bs-toggle="collapse" data-bs-target="#collapse{{ $section->id }}">
                                                    <i class="ti ti-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapse{{ $section->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $section->id }}" data-bs-parent="#sectionsAccordion">
                                        <div class="card-body bg-light border-top">
                                            <div class="row mb-4">
                                                <div class="col-md-12">
                                                    <div class="card border-0 p-3 rounded">
                                                        <div class="row gy-2">
                                                            <div class="col-md-3 border-end">
                                                                <small class="text-uppercase text-muted fw-bold">Items List</small>
                                                                <div class="fw-bold mt-1">{{ count($section->items_list ?? []) }} items</div>
                                                            </div>
                                                            <div class="col-md-3 border-end">
                                                                <small class="text-uppercase text-muted fw-bold">Buttons</small>
                                                                <div class="fw-bold mt-1">{{ count($section->buttons ?? []) }} buttons</div>
                                                            </div>
                                                            <div class="col-md-3 border-end">
                                                                <small class="text-uppercase text-muted fw-bold">Column Width</small>
                                                                <div class="fw-bold mt-1">col-{{ $section->column_width }}</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-uppercase text-muted fw-bold">Background</small>
                                                                <div class="d-flex align-items-center mt-1">
                                                                    <span class="border rounded-circle d-inline-block me-2" style="width: 15px; height: 15px; background-color: {{ $section->background_color }};"></span>
                                                                    <span class="small font-monospace">{{ $section->background_color }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="text-muted text-uppercase small fw-bold mb-3 ps-1">Blocks in this Section</h6>
                                            @if(isset($blocks[$section->id]) && $blocks[$section->id]->count() > 0)
                                                <div class="d-flex flex-column gap-3">
                                                    @foreach($blocks[$section->id] as $block)
                                                        <div class="card border shadow-sm" wire:key="block-{{ $block->id }}">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div class="d-flex gap-3">
                                                                        <div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                            @if($block->type === 'image' && $block->getFirstMediaUrl('content_image'))
                                                                                <img src="{{ $block->getFirstMediaUrl('content_image') }}" class="img-fluid rounded" alt="img">
                                                                            @elseif($block->type === 'image')
                                                                                <i class="ti ti-photo text-muted fs-4"></i>
                                                                            @else
                                                                                <span class="fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">{{ substr($block->type, 0, 3) }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-1 fw-bold {{ $block->is_enabled ? '' : 'text-muted text-decoration-line-through' }}">
                                                                                @if($block->type === 'heading' || $block->type === 'stat' || $block->type === 'feature' || $block->type === 'card' || $block->type === 'default')
                                                                                   {{ $block->heading ?: 'Untitled Block' }}
                                                                                @else
                                                                                   {{ ucfirst($block->type) }} Block
                                                                                @endif
                                                                            </h6>
                                                                            <p class="mb-0 text-muted small text-wrap text-break" style="max-width: 400px;">
                                                                                {{ $block->subheading ?: Str::limit($block->description, 60) }}
                                                                            </p>
                                                                            <div class="mt-2">
                                                                                <span class="badge bg-light text-dark border me-1">{{ $block->type }}</span>
                                                                                <span class="badge bg-light text-dark border">col-{{ $block->column_width }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="btn-group btn-group-sm">
                                                                        <button class="btn btn-ghost-primary" wire:click.prevent="editBlock({{ $block->id }})">
                                                                            <i class="ti ti-pencil"></i>
                                                                        </button>
                                                                        <button class="btn btn-ghost-{{ $block->is_enabled ? 'warning' : 'secondary' }}" wire:click.prevent="toggleBlockStatus({{ $block->id }})">
                                                                            <i class="ti ti-{{ $block->is_enabled ? 'eye' : 'eye-off' }}"></i>
                                                                        </button>
                                                                        <button class="btn btn-ghost-danger" wire:click.prevent="deleteBlock({{ $block->id }})"
                                                                                wire:confirm="Delete this block?">
                                                                            <i class="ti ti-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4 border border-dashed rounded bg-white">
                                                    <p class="text-muted mb-2">No blocks in this section</p>
                                                    <button class="btn btn-sm btn-outline-primary" wire:click.prevent="createBlock({{ $section->id }})">
                                                        <i class="ti ti-plus me-1"></i> Add Block
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="ti ti-layout-grid fs-2 text-muted"></i>
                                </div>
                                <h5 class="fw-bold">Start Building Your Page</h5>
                                <p class="text-muted mb-4">Create sections to organize your content structure.</p>
                                <button class="btn btn-primary" wire:click="createSection">
                                    <i class="ti ti-plus me-1"></i> Create First Section
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SEO & Page Content -->
            <div class="row mt-4">
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom">
                            <h3 class="card-title"><i class="ti ti-align-left me-2 text-primary"></i> Page Content</h3>
                        </div>
                        <div class="card-body">
                            <div class="bg-light p-3 rounded text-wrap text-break" style="min-height: 200px;">
                                {!! nl2br(e($page->content)) ?: '<span class="text-muted fst-italic">No main content. Use sections and blocks to build your page.</span>' !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom">
                            <h3 class="card-title"><i class="ti ti-search me-2 text-info"></i> SEO Metadata</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted fw-bold text-uppercase small w-30 ps-3">Title</td>
                                            <td class="small pe-3 text-wrap">{{ $page->meta_title ?? $page->title }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold text-uppercase small ps-3">Desc</td>
                                            <td class="small pe-3 text-wrap text-break">{{ Str::limit($page->meta_description, 60) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold text-uppercase small ps-3">Keywords</td>
                                            <td class="small pe-3 text-wrap">{{ Str::limit($page->meta_keywords, 50) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold text-uppercase small ps-3">OG Title</td>
                                            <td class="small pe-3 text-wrap">{{ Str::limit($page->og_title, 50) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold text-uppercase small ps-3">Canonical</td>
                                            <td class="small pe-3 text-wrap text-break">{{ Str::limit($page->canonical_url, 40) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top bg-light">
                                <small class="d-block text-muted small mb-2">Detailed Meta Tags</small>
                                <div class="d-flex flex-wrap gap-1">
                                     @if ($page->metaTags && $page->metaTags->count())
                                        @foreach ($page->metaTags as $tag)
                                            <span class="badge bg-white border text-secondary" title="{{ is_array($tag->value) ? json_encode($tag->value) : $tag->value }}">{{ $tag->key }}</span>
                                        @endforeach
                                     @else
                                        <span class="text-muted small">No custom tags</span>
                                     @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Modal -->
    @if($showSectionModal)
        <div class="modal modal-blur show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingSection ? 'Edit Section' : 'Create Section' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeSectionModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form wire:submit.prevent="saveSection" id="sectionForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Title *</label>
                                        <input type="text" class="form-control" wire:model="sectionTitle" required>
                                        @error('sectionTitle') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subtitle</label>
                                        <input type="text" class="form-control" wire:model="sectionSubtitle">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Badge / Overline</label>
                                        <input type="text" class="form-control" wire:model="sectionBadge" placeholder="e.g. New Feature">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description / Content</label>
                                <div x-data="richTextRelay('sectionDescription')" class="rich-text-container" wire:ignore wire:key="editor-section">
                                    <textarea x-ref="relay" class="d-none" wire:model.live="sectionDescription"></textarea>
                                    <div x-ref="editor" class="bg-white" style="height: 200px;"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Image</label>
                                <input type="file" class="form-control" wire:model="sectionImageUpload">
                                @if($sectionImage && !$sectionImageUpload)
                                    <div class="mt-2 text-center bg-light p-2 rounded position-relative">
                                         <img src="{{ $sectionImage }}" alt="Current Image" class="img-fluid" style="max-height: 150px;">
                                         <div class="text-muted small mt-1">Current Image</div>
                                         <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" wire:click="removeSectionImage" title="Remove Image">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @endif
                                @if($sectionImageUpload)
                                    <div class="mt-2 text-center bg-light p-2 rounded">
                                         <img src="{{ $sectionImageUpload->temporaryUrl() }}" alt="Preview" class="img-fluid" style="max-height: 150px;">
                                    </div>
                                @endif
                                @error('sectionImageUpload') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Video URL</label>
                                    <input type="url" class="form-control" wire:model="sectionVideoUrl" placeholder="https://youtube.com/...">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Main Btn Text</label>
                                    <input type="text" class="form-control" wire:model="sectionBtnText" placeholder="Learn More">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Main Btn Link</label>
                                    <input type="text" class="form-control" wire:model="sectionBtnLink" placeholder="#">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Attach Form</label>
                                    <select class="form-control" wire:model="sectionFormId">
                                         <option value="">-- No Form --</option>
                                         @foreach($availableForms as $form)
                                             <option value="{{ $form->id }}">{{ $form->title }}</option>
                                         @endforeach
                                    </select>
                                    <small class="text-muted">Select a form to display in this section.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Icon Type</label>
                                        <select class="form-control" wire:model.live="sectionIconType">
                                            <option value="library">Library (Class)</option>
                                            <option value="upload">Upload Image</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    @if($sectionIconType === 'library')
                                        <div class="mb-3">
                                            <label class="form-label">Icon Class (FontAwesome)</label>
                                            <input type="text" class="form-control" wire:model="sectionIconClass" placeholder="fas fa-heart">
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <label class="form-label">Upload Icon</label>
                                            <input type="file" class="form-control" wire:model="sectionIconUpload">
                                            @if($sectionIconImage && !$sectionIconUpload)
                                                <div class="mt-2 text-center bg-light p-2 rounded position-relative">
                                                    <img src="{{ $sectionIconImage }}" alt="Current Icon" class="img-fluid" style="max-height: 50px;">
                                                    <div class="text-muted small mt-1">Current Icon</div>
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" wire:click="removeSectionIcon" title="Remove Icon">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            @endif
                                            @if($sectionIconUpload)
                                                <div class="mt-2 text-center bg-light p-2 rounded">
                                                     <img src="{{ $sectionIconUpload->temporaryUrl() }}" alt="Preview" class="img-fluid" style="max-height: 50px;">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" wire:model="sectionBackgroundColor">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Buttons</label>
                                <button type="button" class="btn btn-sm btn-outline-primary mb-2" wire:click="addButton('section')">
                                    <i class="ti ti-plus me-1"></i> Add Button
                                </button>
                                @foreach($sectionButtons as $index => $btn)
                                    <div class="row mb-2">
                                        <div class="col-4">
                                            <input type="text" class="form-control" wire:model="sectionButtons.{{ $index }}.text" placeholder="Text">
                                        </div>
                                        <div class="col-4">
                                            <input type="url" class="form-control" wire:model="sectionButtons.{{ $index }}.link" placeholder="Link">
                                        </div>
                                        <div class="col-3">
                                             <select class="form-control" wire:model="sectionButtons.{{ $index }}.style">
                                                <option value="primary">Primary</option>
                                                <option value="secondary">Secondary</option>
                                                <option value="outline-primary">Outline</option>
                                                <option value="success">Success</option>
                                                <option value="danger">Danger</option>
                                             </select>
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-sm btn-danger" wire:click="removeButton('section', {{ $index }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" wire:model="sectionSortOrder" min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Column Width</label>
                                <select class="form-control" wire:model="sectionColumnWidth">
                                    <option value="12">Full Width (12) - 100%</option>
                                    <option value="10">5/6 Width (10) - 83.33%</option>
                                    <option value="8">2/3 Width (8) - 66.67%</option>
                                    <option value="6">Half Width (6) - 50%</option>
                                    <option value="4">1/3 Width (4) - 33.33%</option>
                                    <option value="3">1/4 Width (3) - 25%</option>
                                    <option value="2">1/6 Width (2) - 16.67%</option>
                                </select>
                                <small class="form-text text-muted">Bootstrap grid column width (out of 12)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Items List</label>
                                <button type="button" class="btn btn-sm btn-outline-primary mb-2" wire:click="addItemToList('section')">
                                    <i class="ti ti-plus me-1"></i> Add Item
                                </button>
                                @foreach($sectionItemsList as $index => $item)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" wire:model="sectionItemsList.{{ $index }}" placeholder="Item {{ $index + 1 }}">
                                        <button type="button" class="btn btn-outline-danger" wire:click="removeItemFromList('section', {{ $index }})">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeSectionModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" form="sectionForm">
                            {{ $editingSection ? 'Update Section' : 'Create Section' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Block Modal -->
    @if($showBlockModal)
        <div class="modal modal-blur show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingBlock ? 'Edit Block' : 'Create Block' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeBlockModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form wire:submit.prevent="saveBlock" id="blockForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Block Type</label>
                                    <select class="form-control" wire:model.live="blockType">
                                        <option value="text">Text (Default)</option>
                                        <option value="heading">Heading</option>
                                        <option value="stat">Stat (Number)</option>
                                        <option value="image">Image</option>
                                        <option value="feature">Feature</option>
                                        <option value="card">Card</option>
                                        <option value="business_partners">Business Partner</option>
                                        <option value="project">CMS Project</option>
                                        <option value="testimonials">CMS Testimonials</option>
                                        <option value="service_listing">Services</option>
                                        <option value="blog_listing">Blogs</option>
                                        <option value="course_listing">Courses</option>
                                        <option value="pricing">Pricing Card</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" wire:model="blockSortOrder" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                     <label class="form-label">Badge / Overline</label>
                                     <input type="text" class="form-control" wire:model="blockBadge" placeholder="e.g. Best Value">
                                </div>
                            </div>

                            @if($blockType === 'heading' || $blockType === 'stat' || $blockType === 'feature' || $blockType === 'card' || $blockType === 'pricing')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ $blockType === 'stat' ? 'Number Value (e.g. 25)' : 'Heading' }} *</label>
                                            <input type="text" class="form-control" wire:model="blockHeading" required>
                                            @error('blockHeading') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($blockType === 'pricing')
                            <div class="row mb-3 bg-light p-3 rounded mx-0">
                                <h6 class="fw-bold mb-3 text-primary">Pricing Details</h6>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Currency Symbol (e.g. $)</label>
                                    <input type="text" class="form-control" wire:model="blockItemsList.currency">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Price Amount</label>
                                    <input type="text" class="form-control" wire:model="blockItemsList.price">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Period (e.g. /Mo)</label>
                                    <input type="text" class="form-control" wire:model="blockItemsList.period">
                                </div>
                            </div>
                            @endif

                             @if($blockType === 'stat')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Label (Subheading)</label>
                                            <input type="text" class="form-control" wire:model="blockSubheading" placeholder="YEARS EXPERIENCE">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Suffix (e.g. +)</label>
                                            <input type="text" class="form-control" wire:model="blockSuffix">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(in_array($blockType, ['text', 'feature', 'card', 'stat', 'pricing']))
                             <div class="mb-3">
                                <label class="form-label">Description</label>
                                <div x-data="richTextRelay('blockDescription')" class="rich-text-container" wire:ignore wire:key="editor-block">
                                    <textarea x-ref="relay" class="d-none" wire:model.live="blockDescription"></textarea>
                                    <div x-ref="editor" class="bg-white" style="height: 150px;"></div>
                                </div>
                            </div>
                            @endif

                            @if($blockType === 'business_partners')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Display Mode</label>
                                         <select class="form-control" wire:model.live="blockPartnersDisplayMode">
                                             <option value="all">All Partners</option>
                                             <option value="limit">Limit Count</option>
                                         </select>
                                    </div>
                                    @if($blockPartnersDisplayMode === 'limit')
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Limit Count</label>
                                         <input type="number" class="form-control" wire:model="blockPartnersLimitCount" min="0">
                                          @error('blockPartnersLimitCount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    @endif
                                </div>
                            @endif

                            @if($blockType === 'testimonials')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Display Mode</label>
                                         <select class="form-control" wire:model.live="blockTestimonialsDisplayMode">
                                             <option value="all">All Testimonials</option>
                                             <option value="limit">Limit Count</option>
                                         </select>
                                    </div>
                                    @if($blockTestimonialsDisplayMode === 'limit')
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Limit Count</label>
                                         <input type="number" class="form-control" wire:model="blockTestimonialsLimitCount" min="0">
                                          @error('blockTestimonialsLimitCount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    @endif
                                </div>
                            @endif

                            @if($blockType === 'project')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Display Mode</label>
                                         <select class="form-control" wire:model.live="blockProjectDisplayMode">
                                             <option value="all">All Projects</option>
                                             <option value="category">By Category</option>
                                             <option value="latest">Latest</option>
                                         </select>
                                    </div>
                                    @if($blockProjectDisplayMode === 'category')
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Select Category</label>
                                        <select class="form-control" wire:model="blockProjectCategoryId">
                                            <option value="">Choose Category...</option>
                                            @foreach($projectCategories as $cat)
                                                <option value="{{ $cat->category_id }}">
                                                    {{ is_array($cat->category_name) ? ($cat->category_name[config('app.locale')] ?? '') : $cat->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('blockProjectCategoryId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    @endif
                                </div>
                                <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">Limit Count (Optional)</label>
                                         <input type="number" class="form-control" wire:model="blockProjectLimitCount" min="0" placeholder="e.g 6">
                                          @error('blockProjectLimitCount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            @if($blockType === 'service_listing')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Limit Count (Optional)</label>
                                         <input type="number" class="form-control" wire:model="blockServiceListingLimitCount" min="0" placeholder="e.g 6">
                                          @error('blockServiceListingLimitCount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            @if($blockType === 'blog_listing')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Category (Optional)</label>
                                         <select class="form-control" wire:model="blockBlogListingCategoryId">
                                             <option value="">All Categories</option>
                                             @foreach($blogCategories as $cat)
                                                 <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                             @endforeach
                                         </select>
                                    </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">Limit Count (Optional)</label>
                                         <input type="number" class="form-control" wire:model="blockBlogListingLimitCount" min="0" placeholder="e.g 4">
                                          @error('blockBlogListingLimitCount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                            @if($blockType === 'course_listing')
                                <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">Limit Count (Optional)</label>
                                         <input type="number" class="form-control" wire:model="blockCourseListingLimitCount" min="0" placeholder="e.g 3">
                                          @error('blockCourseListingLimitCount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endif

                             <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Video URL</label>
                                    <input type="url" class="form-control" wire:model="blockVideoUrl" placeholder="https://youtube.com/...">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Main Btn Text</label>
                                    <input type="text" class="form-control" wire:model="blockBtnText" placeholder="Learn More">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Main Btn Link</label>
                                    <input type="text" class="form-control" wire:model="blockBtnLink" placeholder="#">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Image</label>
                                <input type="file" class="form-control" wire:model="blockImageUpload">
                                @if($blockImageUrl && !$blockImageUpload)
                                    <div class="mt-2 text-center bg-light p-2 rounded position-relative">
                                            <img src="{{ $blockImageUrl }}" alt="Current Image" class="img-fluid" style="max-height: 100px;">
                                            <div class="text-muted small mt-1">Current Image</div>
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" wire:click="removeBlockImage" title="Remove Image">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @endif
                                @error('blockImageUpload') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            @if($blockType !== 'image')
                             <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Icon Type</label>
                                        <select class="form-control" wire:model.live="blockIconType">
                                            <option value="library">Library (Class)</option>
                                            <option value="upload">Upload Image</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    @if($blockIconType === 'library')
                                        <div class="mb-3">
                                            <label class="form-label">Icon Class</label>
                                            <input type="text" class="form-control" wire:model="blockIconClass" placeholder="fas fa-star">
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <label class="form-label">Upload Icon</label>
                                            <input type="file" class="form-control" wire:model="blockIconUpload">
                                            @if($blockIconImage && !$blockIconUpload)
                                                <div class="mt-2 text-center bg-light p-2 rounded position-relative">
                                                     <img src="{{ $blockIconImage }}" alt="Current Icon" class="img-fluid" style="max-height: 50px;">
                                                     <div class="text-muted small mt-1">Current Icon</div>
                                                     <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" wire:click="removeBlockIcon" title="Remove Icon">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            @endif
                                            @if($blockIconUpload)
                                                <div class="mt-2 text-center bg-light p-2 rounded">
                                                     <img src="{{ $blockIconUpload->temporaryUrl() }}" alt="Preview" class="img-fluid" style="max-height: 50px;">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" wire:model="blockBackgroundColor">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Column Width</label>
                                    <select class="form-control" wire:model="blockColumnWidth">
                                        <option value="12">Full Width (12) - 100%</option>
                                        <option value="10">5/6 Width (10) - 83.33%</option>
                                        <option value="8">2/3 Width (8) - 66.67%</option>
                                        <option value="6">Half Width (6) - 50%</option>
                                        <option value="4">1/3 Width (4) - 33.33%</option>
                                        <option value="3">1/4 Width (3) - 25%</option>
                                        <option value="2">1/6 Width (2) - 16.67%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Buttons</label>
                                <button type="button" class="btn btn-sm btn-outline-primary mb-2" wire:click="addButton('block')">
                                    <i class="ti ti-plus me-1"></i> Add Button
                                </button>
                                @foreach($blockButtons as $index => $btn)
                                    <div class="row mb-2">
                                        <div class="col-4">
                                            <input type="text" class="form-control" wire:model="blockButtons.{{ $index }}.text" placeholder="Text">
                                        </div>
                                        <div class="col-4">
                                            <input type="url" class="form-control" wire:model="blockButtons.{{ $index }}.link" placeholder="Link">
                                        </div>
                                        <div class="col-3">
                                             <select class="form-control" wire:model="blockButtons.{{ $index }}.style">
                                                <option value="primary">Primary</option>
                                                <option value="secondary">Secondary</option>
                                                <option value="outline-primary">Outline</option>
                                                <option value="success">Success</option>
                                                <option value="danger">Danger</option>
                                             </select>
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-sm btn-danger" wire:click="removeButton('block', {{ $index }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Items List</label>
                                <button type="button" class="btn btn-sm btn-outline-primary mb-2" wire:click="addItemToList('block')">
                                    <i class="ti ti-plus me-1"></i> Add Item
                                </button>
                                @foreach($blockItemsList as $index => $item)
                                    @if(is_numeric($index))
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" wire:model="blockItemsList.{{ $index }}" placeholder="Item {{ $index + 1 }}">
                                        <button type="button" class="btn btn-outline-danger" wire:click="removeItemFromList('block', {{ $index }})">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeBlockModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" form="blockForm">
                            {{ $editingBlock ? 'Update Block' : 'Create Block' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('js')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-editor { min-height: 150px; }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
             Alpine.data('richTextRelay', (model) => ({
                quill: null,
                isTyping: false,

                init() {
                    const editorEl = this.$refs.editor;
                    const relayEl = this.$refs.relay;

                    this.quill = new Quill(editorEl, {
                        theme: 'snow',
                        modules: {
                             toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link', 'clean']
                            ]
                        }
                    });

                    const updateEditorFromRelay = () => {
                        const content = relayEl.value;
                        if (content && this.quill.root.innerHTML !== content) {
                             this.quill.root.innerHTML = content;
                        } else if (!content) {
                             this.quill.root.innerHTML = '';
                        }
                    };

                    updateEditorFromRelay();

                    this.quill.on('text-change', () => {
                        this.isTyping = true;
                        relayEl.value = this.quill.root.innerHTML;
                        relayEl.dispatchEvent(new Event('input'));
                        relayEl.dispatchEvent(new Event('blur'));
                    });

                    Livewire.on('modalOpened', () => {
                        this.isTyping = false;
                        setTimeout(() => updateEditorFromRelay(), 50);
                    });

                    this.$watch('$wire.' + model, (value) => {
                        if (!this.isTyping) {
                            updateEditorFromRelay();
                        }
                    });
                }
             }));
        });
    </script>
    @endpush
</div>
