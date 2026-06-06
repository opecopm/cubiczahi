<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ $page->getTranslation('title', app()->getLocale(), false) ?: $page->slug }}</h2>
                    <div class="text-muted">Slug: {{ $page->slug }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-group">
                        <button wire:click="togglePreviewMode" class="btn btn-outline-primary">
                            <i class="ti ti-{{ $previewMode ? 'pencil' : 'eye' }} me-1"></i>
                            {{ $previewMode ? 'Edit Mode' : 'Preview Mode' }}
                        </button>
                        <button wire:click="savePage" class="btn btn-success">
                            <i class="ti ti-device-floppy me-1"></i> Save Draft
                        </button>
                        <button wire:click="publishPage"
                                class="btn @if($hasChanges) btn-primary @else btn-outline-secondary @endif">
                            <i class="ti ti-send me-1"></i> Publish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-body p-0">
                    @if (!$previewMode)
                        <!-- Edit Mode -->
                        <div class="page-builder-canvas" id="sections-container" style="min-height: 600px;">
                            @if (empty($sections))
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="ti ti-layout-grid text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="text-muted">Start Building Your Page</h5>
                                    <p class="text-muted">Add your first section to begin creating your page.</p>
                                    <button wire:click="addSection" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i> Add Section
                                    </button>
                                </div>
                            @else
                                @foreach ($sections as $section)
                                    <div class="page-builder-section mb-3 position-relative"
                                         data-id="{{ $section['id'] }}"
                                         style="{{ $section['settings'] ? \Modules\CMS\Models\PageBuilderSection::find($section['id'])->getStyleAttributes() : '' }}"
                                         :class="{ 'selected': selectedElement === {{ $section['id'] }} && selectedElementType === 'section' }">

                                        <div class="section-hover-controls position-absolute" style="top: -15px; left: 0; z-index: 10; opacity: 0; transition: opacity 0.3s;">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-light btn-sm section-drag-handle" title="Drag to reorder" style="cursor:grab;">
                                                    <i class="ti ti-grip-vertical"></i>
                                                </button>
                                                <button wire:click="selectElement('section', {{ $section['id'] }})" class="btn btn-primary btn-sm" title="Edit Section">
                                                    <i class="ti ti-pencil"></i>
                                                </button>
                                                <button wire:click="duplicateSection({{ $section['id'] }})" class="btn btn-warning btn-sm" title="Duplicate Section">
                                                    <i class="ti ti-copy"></i>
                                                </button>
                                                <button wire:click="deleteSection({{ $section['id'] }})" class="btn btn-danger btn-sm" title="Delete Section">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="section-label position-absolute" style="top: -15px; right: 0; z-index: 10;">
                                            <span class="badge bg-secondary">Section {{ $loop->iteration }}</span>
                                        </div>

                                        @if (!empty($section['rows']))
                                            <div class="rows-container" data-section-id="{{ $section['id'] }}">
                                            @foreach ($section['rows'] as $row)
                                                <div class="page-builder-row position-relative mb-2"
                                                     data-id="{{ $row['id'] }}"
                                                     :class="{ 'selected': selectedElement === {{ $row['id'] }} && selectedElementType === 'row' }">

                                                    <div class="row-hover-controls position-absolute" style="top: -10px; left: 20px; z-index: 10; opacity: 0; transition: opacity 0.3s;">
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-light btn-sm row-drag-handle" title="Drag to reorder" style="cursor:grab;">
                                                                <i class="ti ti-grip-vertical"></i>
                                                            </button>
                                                            <button wire:click="selectElement('row', {{ $row['id'] }})" class="btn btn-primary btn-sm" title="Edit Row">
                                                                <i class="ti ti-pencil"></i>
                                                            </button>
                                                            <button wire:click="duplicateRow({{ $row['id'] }})" class="btn btn-warning btn-sm" title="Duplicate Row">
                                                                <i class="ti ti-copy"></i>
                                                            </button>
                                                            <button wire:click="deleteRow({{ $row['id'] }})" class="btn btn-danger btn-sm" title="Delete Row">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="row-label position-absolute" style="top: -10px; right: 20px; z-index: 10;">
                                                        <span class="badge bg-info">Row {{ $loop->iteration }}</span>
                                                    </div>

                                                    <div class="row columns-container" data-row-id="{{ $row['id'] }}">
                                                        @if (!empty($row['columns']))
                                                            @foreach ($row['columns'] as $column)
                                                                <div class="col-md-{{ $column['width'] }} mb-2" data-id="{{ $column['id'] }}">
                                                                    <div class="page-builder-column position-relative border p-2"
                                                                         :class="{ 'selected': selectedElement === {{ $column['id'] }} && selectedElementType === 'column' }">

                                                                        <div class="column-hover-controls position-absolute" style="top: -8px; left: 5px; z-index: 10; opacity: 0; transition: opacity 0.3s;">
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button class="btn btn-light btn-sm column-drag-handle" title="Drag to reorder" style="cursor:grab;">
                                                                                    <i class="ti ti-grip-vertical"></i>
                                                                                </button>
                                                                                <button wire:click="selectElement('column', {{ $column['id'] }})" class="btn btn-primary btn-sm" title="Edit Column">
                                                                                    <i class="ti ti-pencil"></i>
                                                                                </button>
                                                                                <div class="btn-group btn-group-sm">
                                                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Column Width">
                                                                                        <i class="ti ti-columns"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu">
                                                                                        @foreach ($columnWidths as $width)
                                                                                            <li><a class="dropdown-item" href="#" wire:click="updateColumnWidth({{ $column['id'] }}, {{ $width }})">{{ $width }} columns</a></li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </div>
                                                                                <button wire:click="duplicateColumn({{ $column['id'] }})" class="btn btn-warning btn-sm" title="Duplicate Column">
                                                                                    <i class="ti ti-copy"></i>
                                                                                </button>
                                                                                <button wire:click="deleteColumn({{ $column['id'] }})" class="btn btn-danger btn-sm" title="Delete Column">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        <div class="column-label position-absolute" style="top: -8px; right: 5px; z-index: 10;">
                                                                            <span class="badge bg-success">{{ $column['width'] }} cols</span>
                                                                        </div>

                                                                        @if (!empty($column['blocks']))
                                                                            <div class="blocks-container" data-column-id="{{ $column['id'] }}">
                                                                            @foreach ($column['blocks'] as $block)
                                                                                <div class="page-builder-block position-relative border p-2 mb-2"
                                                                                     data-id="{{ $block['id'] }}"
                                                                                     :class="{ 'selected': selectedElement === {{ $block['id'] }} && selectedElementType === 'block' }">

                                                                                    <div class="block-hover-controls position-absolute" style="top: -5px; left: 2px; z-index: 10; opacity: 0; transition: opacity 0.3s;">
                                                                                        <div class="btn-group btn-group-sm">
                                                                                            <button class="btn btn-light btn-sm block-drag-handle" title="Drag to reorder" style="cursor:grab;">
                                                                                                <i class="ti ti-grip-vertical"></i>
                                                                                            </button>
                                                                                            <button wire:click="selectElement('block', {{ $block['id'] }})" class="btn btn-primary btn-sm" title="Edit Block">
                                                                                                <i class="ti ti-pencil"></i>
                                                                                            </button>
                                                                                            <button wire:click="duplicateBlock({{ $block['id'] }})" class="btn btn-warning btn-sm" title="Duplicate Block">
                                                                                                <i class="ti ti-copy"></i>
                                                                                            </button>
                                                                                            <button wire:click="deleteBlock({{ $block['id'] }})" class="btn btn-danger btn-sm" title="Delete Block">
                                                                                                <i class="ti ti-trash"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="block-label position-absolute" style="top: -5px; right: 2px; z-index: 10;">
                                                                                        <span class="badge bg-primary">{{ ucfirst($block['block_type']) }}</span>
                                                                                    </div>

                                                                                    <div class="block-preview">
                                                                                        @switch($block['block_type'])
                                                                                            @case('text')
                                                                                                <div class="text-block-preview text-{{ $block['settings']['text_align'] ?? 'left' }}"
                                                                                                     style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 16 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'normal' }} !important;">
                                                                                                    {!! $block['content']['text'] ?? '<p class="text-muted">Empty text block</p>' !!}
                                                                                                </div>
                                                                                                @break
                                                                                            @case('heading')
                                                                                                <div class="heading-block-preview text-{{ $block['content']['alignment'] ?? 'left' }}"
                                                                                                     style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important;
                                                                                                            font-size: {{ $block['settings']['font_size'] ?? 32 }}px !important;
                                                                                                            font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;
                                                                                                            margin-top: {{ $block['settings']['margin_top'] ?? 0 }}px !important;
                                                                                                            margin-bottom: {{ $block['settings']['margin_bottom'] ?? 20 }}px !important;">
                                                                                                    <{{ $block['content']['level'] ?? 'h2' }} style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 32 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;">{{ $block['content']['text'] ?? 'Your Heading Here' }}</{{ $block['content']['level'] ?? 'h2' }}>
                                                                                                </div>
                                                                                                @break
                                                                                            @case('animated-heading')
                                                                                                @if(in_array($block['content']['animation_type'] ?? '', ['wordByWord', 'letterByLetter']))
                                                                                                    <div class="animated-heading-block-preview text-{{ $block['content']['alignment'] ?? 'center' }}"
                                                                                                         style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important;
                                                                                                                font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important;
                                                                                                                font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;
                                                                                                                margin-top: {{ $block['settings']['margin_top'] ?? 0 }}px !important;
                                                                                                                margin-bottom: {{ $block['settings']['margin_bottom'] ?? 30 }}px !important;">
                                                                                                        <{{ $block['content']['level'] ?? 'h2' }} class="animated-text-{{ $block['content']['animation_type'] ?? 'wordByWord' }}"
                                                                                                             data-text="{{ $block['content']['text'] ?? 'Animated Heading' }}"
                                                                                                             data-delay="{{ $block['content']['word_letter_delay'] ?? '0.3s' }}"
                                                                                                             data-effect="{{ $block['content']['word_letter_effect'] ?? 'fade' }}"
                                                                                                             style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;">
                                                                                                        </{{ $block['content']['level'] ?? 'h2' }}>
                                                                                                    </div>
                                                                                                @else
                                                                                                    <div class="animated-heading-block-preview text-{{ $block['content']['alignment'] ?? 'center' }}"
                                                                                                         style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important;
                                                                                                                font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important;
                                                                                                                font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;
                                                                                                                margin-top: {{ $block['settings']['margin_top'] ?? 0 }}px !important;
                                                                                                                margin-bottom: {{ $block['settings']['margin_bottom'] ?? 30 }}px !important;
                                                                                                                animation-name: {{ $block['content']['animation_type'] ?? 'fadeIn' }} !important;
                                                                                                                animation-duration: {{ $block['content']['animation_duration'] ?? '1s' }} !important;
                                                                                                                animation-delay: {{ $block['content']['animation_delay'] ?? '0s' }} !important;
                                                                                                                animation-direction: {{ $block['settings']['animation_settings']['direction'] ?? 'normal' }} !important;
                                                                                                                animation-fill-mode: {{ ($block['settings']['animation_settings']['loop'] === 'true' || $block['settings']['animation_settings']['loop'] === true) ? 'both' : 'forwards' }} !important;
                                                                                                                animation-iteration-count: {{ ($block['settings']['animation_settings']['loop'] === 'true' || $block['settings']['animation_settings']['loop'] === true) ? 'infinite' : '1' }} !important;
                                                                                                                animation-timing-function: ease-in-out !important;">
                                                                                                        <{{ $block['content']['level'] ?? 'h2' }} style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;">{{ $block['content']['text'] ?? 'Animated Heading' }}</{{ $block['content']['level'] ?? 'h2' }}>
                                                                                                    </div>
                                                                                                @endif
                                                                                                @break
                                                                                            @case('image')
                                                                                                <div class="image-block-preview text-{{ $block['settings']['alignment'] ?? 'center' }}">
                                                                                                    @if($block['content']['image_url'])
                                                                                                        <img src="{{ $block['content']['image_url'] }}"
                                                                                                             alt="{{ $block['content']['alt_text'] ?? '' }}"
                                                                                                             class="img-fluid"
                                                                                                             style="width: {{ $block['settings']['width'] ?? '100%' }};">
                                                                                                    @else
                                                                                                        <div class="text-center text-muted p-3 border">
                                                                                                            <i class="ti ti-photo"></i>
                                                                                                            <p class="mb-0">No image selected</p>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                                @break
                                                                                            @case('button')
                                                                                                <div class="button-block-preview text-center">
                                                                                                    <a href="{{ $block['content']['button_url'] ?? '#' }}"
                                                                                                       class="btn btn-primary"
                                                                                                       target="{{ $block['content']['target'] ?? '_self' }}">
                                                                                                        {{ $block['content']['button_text'] ?? 'Button' }}
                                                                                                    </a>
                                                                                                </div>
                                                                                                @break
                                                                                            @case('video')
                                                                                                <div class="video-block-preview">
                                                                                                    @if($block['content']['video_url'])
                                                                                                        <div class="ratio ratio-16x9">
                                                                                                            <iframe src="{{ $block['content']['video_url'] }}" allowfullscreen></iframe>
                                                                                                        </div>
                                                                                                    @else
                                                                                                        <div class="text-center text-muted p-3 border">
                                                                                                            <i class="ti ti-video"></i>
                                                                                                            <p class="mb-0">No video URL</p>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                                @break
                                                                                            @case('spacer')
                                                                                                <div class="spacer-block-preview text-center">
                                                                                                    <div style="height: {{ $block['content']['height'] ?? 50 }}px; background: #f0f0f0; border: 2px dashed #ccc;">
                                                                                                        <small class="text-muted">Spacer ({{ $block['content']['height'] ?? 50 }}px)</small>
                                                                                                    </div>
                                                                                                </div>
                                                                                                @break
                                                                                            @case('divider')
                                                                                                <div class="divider-block-preview">
                                                                                                    <hr style="border-color: {{ $block['content']['color'] ?? '#cccccc' }}; border-style: {{ $block['content']['style'] ?? 'solid' }};">
                                                                                                </div>
                                                                                                @break
                                                                                            @case('html')
                                                                                                <div class="html-block-preview">
                                                                                                    <div class="text-muted">
                                                                                                        <small>HTML Block</small>
                                                                                                        <div class="border p-2 mt-1">
                                                                                                            {!! $block['content']['html'] ?? '<!-- No HTML content -->' !!}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                @break
                                                                                        @endswitch
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                            </div>{{-- /.blocks-container --}}

                                                                            <div class="text-center mt-2">
                                                                                <button wire:click="addBlock({{ $column['id'] }})" class="btn btn-outline-primary btn-sm">
                                                                                    <i class="ti ti-plus me-1"></i> Add Block
                                                                                </button>
                                                                            </div>
                                                                        @else
                                                                            <div class="text-center py-4 text-muted">
                                                                                <i class="ti ti-layout-grid" style="font-size: 2rem;"></i>
                                                                                <p class="mb-2">No blocks yet</p>
                                                                                <button wire:click="addBlock({{ $column['id'] }})" class="btn btn-outline-primary btn-sm">
                                                                                    <i class="ti ti-plus me-1"></i> Add Block
                                                                                </button>
                                                                            </div>
                                                                        @endif

                                                                        <div class="text-center">
                                                                            <div class="dropdown">
                                                                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                                    <i class="ti ti-plus me-1"></i> Add Block
                                                                                </button>
                                                                                <ul class="dropdown-menu">
                                                                                    @foreach ($availableBlocks as $type => $label)
                                                                                        <li><a class="dropdown-item" href="#" wire:click="addBlock({{ $column['id'] }}, '{{ $type }}')">{{ $label }}</a></li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <div class="text-center mt-2">
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <i class="ti ti-plus me-1"></i> Add Column
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @foreach ($columnWidths as $width)
                                                                    <li><a class="dropdown-item" href="#" wire:click="addColumn({{ $row['id'] }}, {{ $width }})">Width {{ $width }}</a></li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>{{-- /.rows-container --}}
                                        @endif

                                        <div class="text-center p-2">
                                            <button wire:click="addRow({{ $section['id'] }})" class="btn btn-outline-secondary btn-sm">
                                                <i class="ti ti-plus me-1"></i> Add Row
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="text-center py-3">
                                    <button wire:click="addSection" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i> Add Section
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Preview Mode -->
                        <div class="page-preview">
                            @if (!empty($sections))
                                @foreach ($sections as $section)
                                    <div class="section" style="{{ $section['settings'] ? \Modules\CMS\Models\PageBuilderSection::find($section['id'])->getStyleAttributes() : '' }}">
                                        @if (!empty($section['rows']))
                                            @foreach ($section['rows'] as $row)
                                                <div class="row" style="{{ $row['settings'] ? \Modules\CMS\Models\PageBuilderRow::find($row['id'])->getStyleAttributes() : '' }}">
                                                    @if (!empty($row['columns']))
                                                        @foreach ($row['columns'] as $column)
                                                            <div class="col-md-{{ $column['width'] }}" style="{{ $column['settings'] ? \Modules\CMS\Models\PageBuilderColumn::find($column['id'])->getStyleAttributes() : '' }}">
                                                                @if (!empty($column['blocks']))
                                                                    @foreach ($column['blocks'] as $block)
                                                                        <div class="block" style="{{ $block['settings'] ? \Modules\CMS\Models\PageBuilderBlock::find($block['id'])->getStyleAttributes() : '' }}">
                                                                            @switch($block['block_type'])
                                                                                @case('text')
                                                                                    <div style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 16 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'normal' }} !important;">
                                                                                        {!! $block['content']['text'] ?? '' !!}
                                                                                    </div>
                                                                                    @break
                                                                                @case('heading')
                                                                                    <div class="heading-block text-{{ $block['content']['alignment'] ?? 'left' }}"
                                                                                         style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important;
                                                                                                font-size: {{ $block['settings']['font_size'] ?? 32 }}px !important;
                                                                                                font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;
                                                                                                margin-top: {{ $block['settings']['margin_top'] ?? 0 }}px !important;
                                                                                                margin-bottom: {{ $block['settings']['margin_bottom'] ?? 20 }}px !important;">
                                                                                        <{{ $block['content']['level'] ?? 'h2' }} style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 32 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;">{{ $block['content']['text'] ?? 'Your Heading Here' }}</{{ $block['content']['level'] ?? 'h2' }}>
                                                                                    </div>
                                                                                    @break
                                                                                @case('animated-heading')
                                                                                    @if(in_array($block['content']['animation_type'] ?? '', ['wordByWord', 'letterByLetter']))
                                                                                        <div class="animated-heading-block text-{{ $block['content']['alignment'] ?? 'center' }}"
                                                                                             style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important;
                                                                                                    font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important;
                                                                                                    font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;
                                                                                                    margin-top: {{ $block['settings']['margin_top'] ?? 0 }}px !important;
                                                                                                    margin-bottom: {{ $block['settings']['margin_bottom'] ?? 30 }}px !important;">
                                                                                            <{{ $block['content']['level'] ?? 'h2' }} class="animated-text-{{ $block['content']['animation_type'] ?? 'wordByWord' }}"
                                                                                                 data-text="{{ $block['content']['text'] ?? 'Animated Heading' }}"
                                                                                                 data-delay="{{ $block['content']['word_letter_delay'] ?? '0.3s' }}"
                                                                                                 data-effect="{{ $block['content']['word_letter_effect'] ?? 'fade' }}"
                                                                                                 style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;">
                                                                                            </{{ $block['content']['level'] ?? 'h2' }}>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="animated-heading-block text-{{ $block['content']['alignment'] ?? 'center' }}"
                                                                                             style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important;
                                                                                                    font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important;
                                                                                                    font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;
                                                                                                    margin-top: {{ $block['settings']['margin_top'] ?? 0 }}px !important;
                                                                                                    margin-bottom: {{ $block['settings']['margin_bottom'] ?? 30 }}px !important;
                                                                                                    animation-name: {{ $block['content']['animation_type'] ?? 'fadeIn' }} !important;
                                                                                                    animation-duration: {{ $block['content']['animation_duration'] ?? '1s' }} !important;
                                                                                                    animation-delay: {{ $block['content']['animation_delay'] ?? '0s' }} !important;
                                                                                                    animation-direction: {{ $block['settings']['animation_settings']['direction'] ?? 'normal' }} !important;
                                                                                                    animation-fill-mode: {{ ($block['settings']['animation_settings']['loop'] === 'true' || $block['settings']['animation_settings']['loop'] === true) ? 'both' : 'forwards' }} !important;
                                                                                                    animation-iteration-count: {{ ($block['settings']['animation_settings']['loop'] === 'true' || $block['settings']['animation_settings']['loop'] === true) ? 'infinite' : '1' }} !important;
                                                                                                    animation-timing-function: ease-in-out !important;">
                                                                                            <{{ $block['content']['level'] ?? 'h2' }} style="color: {{ $block['settings']['text_color'] ?? '#000000' }} !important; font-size: {{ $block['settings']['font_size'] ?? 36 }}px !important; font-weight: {{ $block['settings']['font_weight'] ?? 'bold' }} !important;">{{ $block['content']['text'] ?? 'Animated Heading' }}</{{ $block['content']['level'] ?? 'h2' }}>
                                                                                        </div>
                                                                                    @endif
                                                                                    @break
                                                                                @case('image')
                                                                                    @if($block['content']['image_url'])
                                                                                        <img src="{{ $block['content']['image_url'] }}" alt="{{ $block['content']['alt_text'] ?? '' }}" class="img-fluid">
                                                                                    @endif
                                                                                    @break
                                                                                @case('button')
                                                                                    <a href="{{ $block['content']['button_url'] ?? '#' }}"
                                                                                       class="btn btn-primary"
                                                                                       target="{{ $block['content']['target'] ?? '_self' }}">
                                                                                        {{ $block['content']['button_text'] ?? 'Button' }}
                                                                                    </a>
                                                                                    @break
                                                                                @case('video')
                                                                                    @if($block['content']['video_url'])
                                                                                        <div class="ratio ratio-16x9">
                                                                                            <iframe src="{{ $block['content']['video_url'] }}" allowfullscreen></iframe>
                                                                                        </div>
                                                                                    @endif
                                                                                    @break
                                                                                @case('spacer')
                                                                                    <div style="height: {{ $block['content']['height'] ?? 50 }}px;"></div>
                                                                                    @break
                                                                                @case('divider')
                                                                                    <hr style="border-color: {{ $block['content']['color'] ?? '#cccccc' }}; border-style: {{ $block['content']['style'] ?? 'solid' }};">
                                                                                    @break
                                                                                @case('html')
                                                                                    {!! $block['content']['html'] ?? '' !!}
                                                                                    @break
                                                                            @endswitch
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Panel -->
    @if ($showSettings && $selectedElement)
        <div class="settings-panel position-fixed" id="settingsPanel" style="top: 0; right: 0; width: 350px; height: 100vh; background: white; box-shadow: -2px 0 10px rgba(0,0,0,0.1); z-index: 1000; overflow-y: auto;">
            <div class="sidebar-drag-handle" id="sidebarDragHandle">
                <div class="drag-handle-line"></div>
                <div class="drag-handle-line"></div>
                <div class="drag-handle-line"></div>
            </div>

            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Settings</h6>
                    <button wire:click="deselectElement" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <div class="settings-content">
                    @if ($selectedElementType === 'section')
                        @include('cms::livewire.page-builder.settings.section', ['selectedElement' => $selectedElement])
                    @elseif ($selectedElementType === 'row')
                        @include('cms::livewire.page-builder.settings.row', ['selectedElement' => $selectedElement])
                    @elseif ($selectedElementType === 'column')
                        @include('cms::livewire.page-builder.settings.column', ['columnWidths' => $columnWidths, 'selectedElement' => $selectedElement])
                    @elseif ($selectedElementType === 'block')
                        @include('cms::livewire.page-builder.settings.block', ['blockType' => $blockType, 'selectedElement' => $selectedElement])
                    @endif
                </div>
            </div>
        </div>
    @endif

    <style>
.sortable-ghost { opacity: 0.4; background: #e3f2fd !important; border: 2px dashed #1976d2 !important; }
.sortable-chosen { box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

/* WordPress-style Page Builder */
.page-builder-section,
.page-builder-row,
.page-builder-column,
.page-builder-block {
    border: 2px dashed transparent;
    transition: all 0.3s ease;
    position: relative;
}

.page-builder-section:hover,
.page-builder-row:hover,
.page-builder-column:hover,
.page-builder-block:hover {
    border-color: #007bff;
    background-color: rgba(0, 123, 255, 0.05);
}

.page-builder-section:hover .section-hover-controls,
.page-builder-row:hover .row-hover-controls,
.page-builder-column:hover .column-hover-controls,
.page-builder-block:hover .block-hover-controls {
    opacity: 1 !important;
}

.page-builder-section.selected,
.page-builder-row.selected,
.page-builder-column.selected,
.page-builder-block.selected {
    border-color: #007bff !important;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    background-color: rgba(0, 123, 255, 0.1);
}

.page-builder-section.selected .section-hover-controls,
.page-builder-row.selected .row-hover-controls,
.page-builder-column.selected .column-hover-controls,
.page-builder-block.selected .block-hover-controls {
    opacity: 1 !important;
}

.section-hover-controls,
.row-hover-controls,
.column-hover-controls,
.block-hover-controls {
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    padding: 2px;
}

.section-hover-controls .btn,
.row-hover-controls .btn,
.column-hover-controls .btn,
.block-hover-controls .btn {
    border-radius: 3px;
    margin: 0 1px;
}

.section-label,
.row-label,
.column-label,
.block-label {
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    padding: 2px 6px;
}

.settings-panel {
    animation: slideIn 0.3s ease-out;
    position: relative;
    border-left: 3px solid transparent;
    transition: border-left-color 0.2s ease;
}

.settings-panel:hover {
    border-left-color: rgba(0, 123, 255, 0.3);
}

@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

.sidebar-drag-handle {
    position: absolute;
    left: -8px;
    top: 0;
    width: 16px;
    height: 100%;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-right: none;
    border-radius: 8px 0 0 8px;
    cursor: col-resize;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 3px;
    transition: all 0.2s ease;
    z-index: 1001;
    pointer-events: auto;
}

.sidebar-drag-handle:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.sidebar-drag-handle:active {
    background: #007bff;
    border-color: #0056b3;
}

.drag-handle-line {
    width: 2px;
    height: 12px;
    background: #6c757d;
    border-radius: 1px;
    transition: all 0.2s ease;
}

.sidebar-drag-handle:hover .drag-handle-line {
    background: #007bff;
}

.sidebar-drag-handle:active .drag-handle-line {
    background: white;
}

.sidebar-drag-handle::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    background: transparent;
    z-index: 1;
}

.sidebar-drag-handle:hover::before {
    background: rgba(0, 123, 255, 0.1);
}

.sidebar-drag-handle:active::before {
    background: rgba(0, 123, 255, 0.2);
}

.wp-editor-tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 0;
}

.wp-editor-tab {
    background: #f1f1f1;
    border: 1px solid #ddd;
    border-bottom: none;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    color: #555;
    transition: all 0.2s ease;
    border-radius: 4px 4px 0 0;
    margin-right: 2px;
}

.wp-editor-tab:hover {
    background: #f9f9f9;
    color: #333;
}

.wp-editor-tab.active {
    background: white;
    color: #0073aa;
    border-bottom: 1px solid white;
    margin-bottom: -1px;
    z-index: 1;
    position: relative;
}

.editor-container {
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    background: white;
}

#html-editor {
    border: none;
    border-radius: 0;
    resize: vertical;
    min-height: 200px;
}

#html-editor:focus {
    box-shadow: none;
    border-color: transparent;
}

.empty-state {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    color: #6c757d;
}

.empty-state:hover {
    border-color: #007bff;
    background-color: rgba(0, 123, 255, 0.05);
}

.block-preview {
    min-height: 50px;
    padding: 10px;
}

.text-block-preview {
    font-size: 14px;
    line-height: 1.5;
}

.text-block-preview h1,
.text-block-preview h2,
.text-block-preview h3,
.text-block-preview h4,
.text-block-preview h5,
.text-block-preview h6 {
    text-decoration: none !important;
    border-bottom: none !important;
}

.text-block-preview a { text-decoration: none; color: inherit; }
.text-block-preview a:hover { text-decoration: underline; }
.image-block-preview img { max-width: 100%; height: auto; }
.button-block-preview .btn { font-size: 14px; padding: 8px 16px; }

.spacer-block-preview {
    display: flex;
    align-items: center;
    justify-content: center;
}

.spacer-block-preview small {
    position: absolute;
    background: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
}

.settings-field {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    background-color: #ffffff;
    transition: all 0.3s ease;
}

.settings-field:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
}

.settings-field .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; }
.settings-field .form-control { border: 1px solid #ced4da; border-radius: 4px; padding: 8px 12px; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; }
.settings-field .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }
.settings-field .form-control-color { width: 50px; height: 38px; padding: 2px; }
.settings-field input[type="file"] { padding: 8px; border: 2px dashed #007bff; border-radius: 4px; background-color: #f8f9fa; transition: all 0.3s ease; }
.settings-field input[type="file"]:hover { border-color: #0056b3; background-color: #e3f2fd; }
.settings-field input[type="file"]:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }

.image-preview-container { text-align: center; padding: 10px; background-color: #f8f9fa; border-radius: 4px; border: 1px solid #dee2e6; }
.image-preview-container img { border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

.section-settings h6, .row-settings h6, .column-settings h6, .block-settings h6 { color: #007bff; font-weight: 700; padding-bottom: 8px; margin-bottom: 20px; }

.heading-block-preview, .heading-block { line-height: 1.2; }
.animated-heading-block-preview, .animated-heading-block { line-height: 1.2; }

.page-builder-canvas .text-block-preview,
.page-builder-canvas .heading-block-preview,
.page-builder-canvas .animated-heading-block-preview,
.page-preview .heading-block,
.page-preview .animated-heading-block { display: block !important; }

.page-builder-canvas .text-block-preview *,
.page-builder-canvas .heading-block-preview *,
.page-builder-canvas .animated-heading-block-preview *,
.page-preview .heading-block *,
.page-preview .animated-heading-block * { color: inherit !important; font-size: inherit !important; font-weight: inherit !important; margin: inherit !important; padding: inherit !important; }

.page-builder-canvas .text-block-preview p,
.page-builder-canvas .text-block-preview div,
.page-builder-canvas .text-block-preview span,
.page-builder-canvas .text-block-preview h1,
.page-builder-canvas .text-block-preview h2,
.page-builder-canvas .text-block-preview h3,
.page-builder-canvas .text-block-preview h4,
.page-builder-canvas .text-block-preview h5,
.page-builder-canvas .text-block-preview h6 { color: inherit !important; font-size: inherit !important; font-weight: inherit !important; }

.page-builder-canvas .text-left, .page-preview .text-left { text-align: left !important; }
.page-builder-canvas .text-center, .page-preview .text-center { text-align: center !important; }
.page-builder-canvas .text-right, .page-preview .text-right { text-align: right !important; }

.animated-heading-block-preview, .animated-heading-block { animation-play-state: running !important; }

@keyframes animationRestart { 0% { animation-play-state: paused; } 100% { animation-play-state: running; } }

.animated-text-wordByWord .word, .animated-text-letterByLetter .letter { opacity: 0; display: inline-block; }
.animated-text-wordByWord .word.fade, .animated-text-letterByLetter .letter.fade { animation: fadeInWord 0.5s ease-in-out forwards; }
.animated-text-wordByWord .word.slide, .animated-text-letterByLetter .letter.slide { animation: slideInWord 0.5s ease-in-out forwards; }
.animated-text-wordByWord .word.bounce, .animated-text-letterByLetter .letter.bounce { animation: bounceInWord 0.5s ease-in-out forwards; }
.animated-text-wordByWord .word.zoom, .animated-text-letterByLetter .letter.zoom { animation: zoomInWord 0.5s ease-in-out forwards; }

@keyframes fadeInWord { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideInWord { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes bounceInWord { 0% { opacity: 0; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.1); } 100% { opacity: 1; transform: scale(1); } }
@keyframes zoomInWord { from { opacity: 0; transform: scale(0.5); } to { opacity: 1; transform: scale(1); } }

@keyframes fadeIn { from { opacity: 0.8; } to { opacity: 1; } }
@keyframes slideInUp { from { opacity: 0.8; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInDown { from { opacity: 0.8; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInLeft { from { opacity: 0.8; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
@keyframes slideInRight { from { opacity: 0.8; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
@keyframes zoomIn { from { opacity: 0.8; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
@keyframes bounceIn { 0% { opacity: 0.8; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.05); } 70% { transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
@keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.8; } 100% { transform: scale(1); opacity: 1; } }

@media (max-width: 768px) {
    .heading-block-preview, .heading-block { font-size: 24px !important; }
    .animated-heading-block-preview, .animated-heading-block { font-size: 28px !important; }
}

@media (max-width: 576px) {
    .heading-block-preview, .heading-block { font-size: 20px !important; }
    .animated-heading-block-preview, .animated-heading-block { font-size: 24px !important; }
}

@media (min-width: 1200px) {
    .heading-block-preview, .heading-block { font-size: 36px !important; }
    .animated-heading-block-preview, .animated-heading-block { font-size: 42px !important; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
// ─── Sortable drag-and-drop ───────────────────────────────────────────────────
const sortableInstances = [];

function getWire() {
    // Walk up from the sections canvas to find THIS component's wire:id
    const canvas = document.getElementById('sections-container');
    if (!canvas) return null;
    const root = canvas.closest('[wire\\:id]');
    return root ? Livewire.find(root.getAttribute('wire:id')) : null;
}

function destroySortables() {
    sortableInstances.forEach(s => s.destroy());
    sortableInstances.length = 0;
}

function initSortables() {
    destroySortables();

    // Sections
    const sectionsEl = document.getElementById('sections-container');
    if (sectionsEl) {
        sortableInstances.push(Sortable.create(sectionsEl, {
            animation: 150,
            handle: '.section-drag-handle',
            draggable: '.page-builder-section',
            ghostClass: 'sortable-ghost',
            onEnd(evt) {
                const ids = [...sectionsEl.querySelectorAll(':scope > .page-builder-section')]
                    .map(el => parseInt(el.dataset.id));
                getWire()?.call('reorderItems', 'section', ids);
            }
        }));
    }

    // Rows (one sortable per section)
    document.querySelectorAll('.rows-container').forEach(container => {
        sortableInstances.push(Sortable.create(container, {
            animation: 150,
            handle: '.row-drag-handle',
            draggable: '.page-builder-row',
            ghostClass: 'sortable-ghost',
            onEnd(evt) {
                const ids = [...container.querySelectorAll(':scope > .page-builder-row')]
                    .map(el => parseInt(el.dataset.id));
                getWire()?.call('reorderItems', 'row', ids);
            }
        }));
    });

    // Columns (one sortable per row)
    document.querySelectorAll('.columns-container').forEach(container => {
        sortableInstances.push(Sortable.create(container, {
            animation: 150,
            handle: '.column-drag-handle',
            draggable: '[data-id]',
            ghostClass: 'sortable-ghost',
            onEnd(evt) {
                const ids = [...container.querySelectorAll(':scope > [data-id]')]
                    .map(el => parseInt(el.dataset.id));
                getWire()?.call('reorderItems', 'column', ids);
            }
        }));
    });

    // Blocks (one sortable per column)
    document.querySelectorAll('.blocks-container').forEach(container => {
        sortableInstances.push(Sortable.create(container, {
            animation: 150,
            handle: '.block-drag-handle',
            draggable: '.page-builder-block',
            ghostClass: 'sortable-ghost',
            onEnd(evt) {
                const ids = [...container.querySelectorAll(':scope > .page-builder-block')]
                    .map(el => parseInt(el.dataset.id));
                getWire()?.call('reorderItems', 'block', ids);
            }
        }));
    });
}

document.addEventListener('DOMContentLoaded', initSortables);
document.addEventListener('livewire:updated', () => setTimeout(initSortables, 150));

// ─── Sidebar resize ───────────────────────────────────────────────────────────
(function() {
    let isDragging = false, startX = 0, startWidth = 0, sidebar = null;

    function getSidebar() { return document.getElementById('settingsPanel'); }

    function initSidebarDrag() {
        sidebar = getSidebar();
        if (!sidebar) { setTimeout(initSidebarDrag, 100); return; }
        const savedWidth = localStorage.getItem('sidebarWidth');
        if (savedWidth) sidebar.style.width = savedWidth;
    }

    document.addEventListener('mousedown', function(e) {
        sidebar = getSidebar();
        if (!sidebar) return;
        const handle = document.getElementById('sidebarDragHandle');
        const inHandle = handle && handle.contains(e.target);
        const nearEdge  = sidebar.contains(e.target) && (e.clientX - sidebar.getBoundingClientRect().left) <= 20;
        if (inHandle || nearEdge) {
            isDragging = true;
            startX = e.clientX;
            startWidth = parseInt(window.getComputedStyle(sidebar).width, 10);
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
            e.preventDefault();
        }
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging || !sidebar) return;
        const w = Math.min(600, Math.max(250, startWidth + (startX - e.clientX)));
        sidebar.style.width = w + 'px';
    });

    document.addEventListener('mouseup', function() {
        if (isDragging && sidebar) {
            isDragging = false;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            localStorage.setItem('sidebarWidth', sidebar.style.width);
        }
    });

    document.addEventListener('DOMContentLoaded', initSidebarDrag);
    document.addEventListener('livewire:updated', () => setTimeout(initSidebarDrag, 100));
})();

// ─── Quill editor ─────────────────────────────────────────────────────────────
let quill = null;
let currentEditorMode = 'visual';

function switchEditorMode(mode) {
    const visualTab = document.getElementById('visualTab');
    const textTab   = document.getElementById('textTab');
    const visualEditor = document.getElementById('visualEditor');
    const textEditor   = document.getElementById('textEditor');
    if (mode === 'visual') {
        visualTab?.classList.add('active');
        textTab?.classList.remove('active');
        if (visualEditor) visualEditor.style.display = 'block';
        if (textEditor)   textEditor.style.display   = 'none';
        currentEditorMode = 'visual';
        if (!quill && document.getElementById('text-editor')) initializeQuill();
    } else {
        visualTab?.classList.remove('active');
        textTab?.classList.add('active');
        if (visualEditor) visualEditor.style.display = 'none';
        if (textEditor)   textEditor.style.display   = 'block';
        currentEditorMode = 'text';
        if (quill) document.getElementById('html-editor').value = quill.root.innerHTML;
    }
}

function initializeQuill() {
    if (quill) return;
    quill = new Quill('#text-editor', {
        theme: 'snow',
        modules: { toolbar: [
            [{ header: [1,2,3,4,5,6,false] }],
            ['bold','italic','underline','strike'],
            [{ color: [] }, { background: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ align: [] }],
            ['link','image'],
            ['clean']
        ]}
    });
    const wire = getWire();
    if (wire) { const t = wire.get('content.text'); if (t) quill.root.innerHTML = t; }
    quill.on('text-change', () => getWire()?.call('updateTextContent', quill.root.innerHTML));
}

document.addEventListener('livewire:updated', () => {
    setTimeout(() => {
        if (document.getElementById('text-editor') && currentEditorMode === 'visual') initializeQuill();
    }, 100);
});

document.addEventListener('input', function(e) {
    if (e.target.id === 'html-editor') getWire()?.call('updateTextContent', e.target.value);
});

window.switchEditorMode = switchEditorMode;

// ─── Animated text ────────────────────────────────────────────────────────────
function initWordLetterAnimations() {
    document.querySelectorAll('.animated-text-wordByWord').forEach(function(el) {
        const text = el.getAttribute('data-text') || '';
        const delay = el.getAttribute('data-delay') || '0.3s';
        const effect = el.getAttribute('data-effect') || 'fade';
        el.innerHTML = '';
        text.split(' ').forEach(function(word, i) {
            const span = document.createElement('span');
            span.className = 'word';
            span.textContent = word + (i < text.split(' ').length - 1 ? ' ' : '');
            span.style.animationDelay = (i * parseFloat(delay) * 1000) + 'ms';
            el.appendChild(span);
        });
        setTimeout(() => el.querySelectorAll('.word').forEach(w => w.classList.add(effect)), 100);
    });

    document.querySelectorAll('.animated-text-letterByLetter').forEach(function(el) {
        const text = el.getAttribute('data-text') || '';
        const delay = el.getAttribute('data-delay') || '0.1s';
        const effect = el.getAttribute('data-effect') || 'fade';
        el.innerHTML = '';
        text.split('').forEach(function(char, i) {
            const span = document.createElement('span');
            span.className = 'letter';
            span.textContent = char;
            span.style.animationDelay = (i * parseFloat(delay) * 1000) + 'ms';
            el.appendChild(span);
        });
        setTimeout(() => el.querySelectorAll('.letter').forEach(l => l.classList.add(effect)), 100);
    });
}

document.addEventListener('DOMContentLoaded', initWordLetterAnimations);
document.addEventListener('livewire:updated', () => setTimeout(initWordLetterAnimations, 100));
</script>
</div>
