@php
    $currentLang = $activeLanguages->where('code', $activeLocale)->first();
    $currentDir = $currentLang?->direction ?? 'ltr';
@endphp
<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Edit Page</h2>
                    <div class="text-secondary small">{{ is_array($page['title']) ? ($page['title'][$activeLocale] ?? '') : ($page['title'] ?? '') }}</div>
                </div>
                <div class="col-auto ms-auto d-flex align-items-center gap-2">
                    <select class="form-select border-secondary text-secondary fw-semibold" id="language-switcher" style="width: auto; height: 36px; padding-top: 4px; padding-bottom: 4px;">
                        @foreach ($activeLanguages as $lang)
                            <option value="{{ $lang->code }}" {{ $activeLocale === $lang->code ? 'selected' : '' }}>
                                🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-outline-secondary" style="height: 36px; display: inline-flex; align-items: center;">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            @include('components.tabler.alerts')

            <form wire:submit.prevent="save">
                <div class="row g-3">

                    {{-- Main Content Card --}}
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Page Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Title ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('page.title.'.$activeLocale) is-invalid @enderror"
                                               wire:model.defer="page.title.{{ $activeLocale }}" wire:blur="generateSlug"
                                               dir="{{ $currentDir }}">
                                        @error('page.title.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Slug</label>
                                        <input type="text" class="form-control @error('page.slug') is-invalid @enderror"
                                               wire:model.defer="page.slug">
                                        @error('page.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Subtitle ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('page.subtitle.'.$activeLocale) is-invalid @enderror"
                                               wire:model.defer="page.subtitle.{{ $activeLocale }}"
                                               dir="{{ $currentDir }}">
                                        @error('page.subtitle.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Alternative Title ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('page.alternative_title.'.$activeLocale) is-invalid @enderror"
                                               wire:model.defer="page.alternative_title.{{ $activeLocale }}"
                                               dir="{{ $currentDir }}">
                                        @error('page.alternative_title.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                     <div class="col-12">
                                        <style>
                                            .ql-toolbar.ql-snow {
                                                border: none !important;
                                                border-bottom: 1px solid #e6e7e9 !important;
                                                background-color: #f8f9fa;
                                                border-top-left-radius: 4px;
                                                border-top-right-radius: 4px;
                                            }
                                            .ql-container.ql-snow {
                                                border: none !important;
                                                border-bottom-left-radius: 4px;
                                                border-bottom-right-radius: 4px;
                                            }
                                            .blade-tag {
                                                display: none !important;
                                            }
                                        </style>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Content ({{ strtoupper($activeLocale) }})</label>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-primary" id="btn-editor-visual">
                                                    <i class="ti ti-eye me-1"></i> Visual
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="btn-editor-code">
                                                    <i class="ti ti-code me-1"></i> HTML / Code
                                                </button>
                                            </div>
                                        </div>
                                        <div wire:ignore class="border rounded bg-white">
                                            <!-- Visual Editor (Quill) -->
                                            <div id="visual-editor-container">
                                                <div id="content" style="min-height:300px; border:none;"></div>
                                            </div>
                                            
                                            <!-- Code / HTML Editor -->
                                            <div id="code-editor-container" class="d-none">
                                                <textarea id="code-content" 
                                                          class="form-control border-0 font-monospace" 
                                                          rows="14" 
                                                          style="min-height:300px; font-family: 'Courier New', Courier, monospace; font-size: 14px; line-height: 1.5; resize: vertical;" 
                                                          dir="ltr"
                                                          placeholder="Write or paste raw HTML code here..."></textarea>
                                            </div>
                                        </div>
                                        @error('page.content.'.$activeLocale) <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Icon Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Page Icon</h3>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="iconTabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#icon-class-pane" type="button">
                                            <i class="ti ti-code me-1"></i> Icon Class
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#icon-upload-pane" type="button">
                                            <i class="ti ti-upload me-1"></i> Upload Image
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content border border-top-0 rounded-bottom p-3">
                                    <div class="tab-pane fade show active" id="icon-class-pane">
                                        <label class="form-label">Icon Class (FontAwesome / Tabler / Bootstrap)</label>
                                        <input type="text" class="form-control @error('page.icon') is-invalid @enderror"
                                               wire:model.defer="page.icon"
                                               placeholder="e.g. ti ti-home, fa fa-user">
                                        <div class="form-hint">Enter the CSS class or paste SVG code.</div>
                                        @error('page.icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="tab-pane fade" id="icon-upload-pane">
                                        <label class="form-label">Upload Icon Image</label>
                                        <input type="file" class="form-control @error('iconFile') is-invalid @enderror"
                                               id="iconFileInput" wire:model="iconFile" accept="image/*">
                                        <div class="form-hint">Max 2MB. SVG, PNG, JPG, WebP.</div>
                                        @error('iconFile') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        @if($iconFile)
                                            <div class="mt-2">
                                                <img src="{{ $iconFile->temporaryUrl() }}" alt="Preview"
                                                     class="rounded border" style="max-width:100px;max-height:100px;">
                                            </div>
                                        @elseif($iconUrl)
                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                <img src="{{ $iconUrl }}" alt="Current Icon"
                                                     class="rounded border" style="max-width:80px;max-height:80px;">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        wire:click="removeIconImage">
                                                    <i class="ti ti-trash me-1"></i> Remove
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Breadcrumb / Video Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Breadcrumb Image or Video</h3>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="mediaTabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#breadcrumb-pane" type="button">
                                            <i class="ti ti-photo me-1"></i> Image
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#video-pane" type="button">
                                            <i class="ti ti-video me-1"></i> Video
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content border border-top-0 rounded-bottom p-3">
                                    {{-- Image Tab --}}
                                    <div class="tab-pane fade show active" id="breadcrumb-pane">
                                        <div class="mb-3">
                                            <label class="form-label">Image URL</label>
                                            <input type="text" class="form-control @error('page.breadcrumb_image') is-invalid @enderror"
                                                   wire:model.live="page.breadcrumb_image"
                                                   placeholder="e.g. img/slider/1.jpg or https://example.com/image.jpg">
                                            <div class="form-hint">Enter URL or path relative to public folder.</div>
                                            @error('page.breadcrumb_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @if(isset($page['breadcrumb_image']) && !empty($page['breadcrumb_image']) && !$breadcrumbImageFile)
                                                @php
                                                    $previewImage = $page['breadcrumb_image'];
                                                    $previewUrl = filter_var($previewImage, FILTER_VALIDATE_URL)
                                                        ? $previewImage
                                                        : (file_exists(public_path($previewImage)) ? url($previewImage) : null);
                                                @endphp
                                                @if($previewUrl)
                                                    <div class="mt-2">
                                                        <img src="{{ $previewUrl }}" alt="Preview"
                                                             class="rounded border" style="max-width:300px;max-height:150px;"
                                                             onerror="this.style.display='none'">
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="hr-text hr-text-center my-3">OR</div>
                                        <div>
                                            <label class="form-label">Upload from Computer</label>
                                            <input type="file" class="form-control @error('breadcrumbImageFile') is-invalid @enderror"
                                                   id="breadcrumbImageFileInput" wire:model="breadcrumbImageFile" accept="image/*">
                                            <div class="form-hint">Max 5MB. JPG, PNG, GIF, WebP.</div>
                                            @error('breadcrumbImageFile') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            @if($breadcrumbImageFile)
                                                <div class="mt-2">
                                                    <img src="{{ $breadcrumbImageFile->temporaryUrl() }}" alt="Preview"
                                                         class="rounded border" style="max-width:200px;max-height:150px;">
                                                </div>
                                            @elseif($breadcrumbUrl && !($page['breadcrumb_image'] ?? null))
                                                <div class="mt-2 d-flex align-items-center gap-2">
                                                    <img src="{{ $breadcrumbUrl }}" alt="Current Breadcrumb"
                                                         class="rounded border" style="max-height:120px;">
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            wire:click="removeBreadcrumbImage">
                                                        <i class="ti ti-trash me-1"></i> Remove
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Video Tab --}}
                                    <div class="tab-pane fade" id="video-pane">
                                        <div class="mb-3">
                                            <label class="form-label">Video URL (YouTube / Vimeo)</label>
                                            <input type="text" class="form-control @error('page.video_url') is-invalid @enderror"
                                                   wire:model.defer="page.video_url"
                                                   placeholder="https://www.youtube.com/watch?v=...">
                                            @error('page.video_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="hr-text hr-text-center my-3">OR</div>
                                        <div>
                                            <label class="form-label">Upload Video from Computer</label>
                                            <input type="file" class="form-control @error('videoFile') is-invalid @enderror"
                                                   id="videoFileInput" wire:model="videoFile" accept="video/*">
                                            <div class="form-hint">Max 50MB. MP4, AVI, MOV, WebM.</div>
                                            @error('videoFile') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            @if($videoFile)
                                                <div class="mt-2 text-success small">
                                                    <i class="ti ti-check me-1"></i>
                                                    {{ $videoFile->getClientOriginalName() }}
                                                    ({{ number_format($videoFile->getSize() / 1024 / 1024, 2) }} MB)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">SEO / Meta Tags</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Meta Description ({{ strtoupper($activeLocale) }})</label>
                                        <textarea class="form-control @error('page.meta_description.'.$activeLocale) is-invalid @enderror"
                                                  rows="3" wire:model.defer="page.meta_description.{{ $activeLocale }}"
                                                  dir="{{ $currentDir }}"></textarea>
                                        @error('page.meta_description.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Meta Keywords ({{ strtoupper($activeLocale) }})</label>
                                        <textarea class="form-control @error('page.meta_keywords.'.$activeLocale) is-invalid @enderror"
                                                  rows="2" wire:model.defer="page.meta_keywords.{{ $activeLocale }}"
                                                  dir="{{ $currentDir }}"></textarea>
                                        @error('page.meta_keywords.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Canonical URL</label>
                                        <input type="text" class="form-control @error('page.canonical_url') is-invalid @enderror"
                                               wire:model.defer="page.canonical_url">
                                        @error('page.canonical_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Open Graph Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Open Graph Tags</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @php
                                        $ogFields = [
                                            'og_title'       => ['label' => 'OG Title', 'translatable' => true],
                                            'og_description' => ['label' => 'OG Description', 'translatable' => true],
                                            'og_url'         => ['label' => 'OG URL', 'translatable' => false],
                                            'og_type'        => ['label' => 'OG Type', 'translatable' => false],
                                            'og_site_name'   => ['label' => 'OG Site Name', 'translatable' => false],
                                            'og_locale'      => ['label' => 'OG Locale', 'translatable' => false],
                                        ];
                                    @endphp
                                    @foreach ($ogFields as $field => $info)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $info['label'] }} {{ $info['translatable'] ? '(' . strtoupper($activeLocale) . ')' : '' }}</label>
                                            @if($info['translatable'])
                                                <input type="text" class="form-control @error('page.'.$field.'.'.$activeLocale) is-invalid @enderror"
                                                       wire:model.defer="page.{{ $field }}.{{ $activeLocale }}"
                                                       dir="{{ $currentDir }}">
                                                @error('page.'.$field.'.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @else
                                                <input type="text" class="form-control @error('page.'.$field) is-invalid @enderror"
                                                       wire:model.defer="page.{{ $field }}">
                                                @error('page.'.$field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="col-md-6">
                                        <label class="form-label">Published Time</label>
                                        <input type="datetime-local" class="form-control @error('page.published_time') is-invalid @enderror"
                                               wire:model.defer="page.published_time">
                                        @error('page.published_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Modified Time</label>
                                        <input type="datetime-local" class="form-control @error('page.modified_time') is-invalid @enderror"
                                               wire:model.defer="page.modified_time">
                                        @error('page.modified_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Twitter Tags Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Twitter Tags</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @php
                                        $twitterFields = [
                                            'twitter_card'        => ['label' => 'Twitter Card', 'translatable' => false],
                                            'twitter_title'       => ['label' => 'Twitter Title', 'translatable' => true],
                                            'twitter_description' => ['label' => 'Twitter Description', 'translatable' => true],
                                        ];
                                    @endphp
                                    @foreach ($twitterFields as $field => $info)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $info['label'] }} {{ $info['translatable'] ? '(' . strtoupper($activeLocale) . ')' : '' }}</label>
                                            @if($info['translatable'])
                                                <input type="text" class="form-control @error('page.'.$field.'.'.$activeLocale) is-invalid @enderror"
                                                       wire:model.defer="page.{{ $field }}.{{ $activeLocale }}"
                                                       dir="{{ $currentDir }}">
                                                @error('page.'.$field.'.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @else
                                                <input type="text" class="form-control @error('page.'.$field) is-invalid @enderror"
                                                       wire:model.defer="page.{{ $field }}">
                                                @error('page.'.$field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select @error('page.status') is-invalid @enderror"
                                            wire:model.defer="page.status">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                    @error('page.status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Page Type</label>
                                    <select class="form-select @error('page.page_type') is-invalid @enderror"
                                            wire:model.defer="page.page_type">
                                        <option value="default">Default</option>
                                        <option value="service">Service</option>
                                        <option value="portfolio">Portfolio</option>
                                        <option value="blog">Blog</option>
                                    </select>
                                    @error('page.page_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Template Type</label>
                                    <select class="form-select @error('page.template_type') is-invalid @enderror"
                                            wire:model.defer="page.template_type">
                                        <option value="default">Default</option>
                                        <option value="custom">Custom</option>
                                        <option value="page_builder">Page Builder</option>
                                    </select>
                                    @error('page.template_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Template Name</label>
                                    <input type="text" class="form-control @error('page.template_name') is-invalid @enderror"
                                           wire:model.defer="page.template_name">
                                    @error('page.template_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Parent Page</label>
                                    <select class="form-select @error('page.parent_id') is-invalid @enderror"
                                            wire:model.defer="page.parent_id">
                                        <option value="">— None —</option>
                                        @foreach ($parentPages as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('page.parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Published At</label>
                                    <input type="date" class="form-control @error('page.published_at') is-invalid @enderror"
                                           wire:model.defer="page.published_at">
                                    @error('page.published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-device-floppy me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                let contentElement = document.getElementById('content');
                const codeEditor = document.getElementById('code-content');
                
                if (contentElement && codeEditor) {
                    let quillInstance = null;
                    
                    // Initialize editors with active locale content
                    const activeLocale = @this.get('activeLocale');
                    const pageContent = @this.get('page.content');
                    const initialContent = (pageContent && pageContent[activeLocale]) ? pageContent[activeLocale] : '';
                    
                    // Keep track of current editor mode (visual or code)
                    let currentMode = 'visual'; 
                    
                    // Toggle button DOM elements
                    const btnVisual = document.getElementById('btn-editor-visual');
                    const btnCode = document.getElementById('btn-editor-code');
                    const visualContainer = document.getElementById('visual-editor-container');
                    const codeContainer = document.getElementById('code-editor-container');
                    
                    // Helper to detect complex HTML (sections, grids, customized structures)
                    function isComplex(html) {
                        if (!html) return false;
                        return /<(form|input|textarea|select|button|section|style|script|iframe)\b/i.test(html) || 
                               /@(foreach|endforeach|if|endif|else|elseif|unless|endunless|while|endwhile|switch|case|default|endswitch)\b/i.test(html);
                    }


                    
                    // Helper to temporarily hide Blade tags visually in visual mode
                    function wrapBladeTags(html) {
                        if (!html) return '';
                        
                        // First unwrap to avoid double-wrapping
                        html = unwrapBladeTags(html);
                        
                        // Split HTML by tags/comments to only process actual text nodes (preserving attributes)
                        const tagOrComment = /(<[^>]+>|<!--[\s\S]*?-->)/g;
                        const parts = html.split(tagOrComment);
                        
                        for (let i = 0; i < parts.length; i++) {
                            // Only process parts that are NOT HTML tags or comments
                            if (i % 2 === 0) {
                                let text = parts[i];
                                
                                // 1. Wrap Blade comments
                                const commentRegex = new RegExp("\\{\\{--" + "[\\s\\S]*?" + "--\\}\\}", "g");
                                text = text.replace(commentRegex, '<span class="blade-tag">$&</span>');
                                
                                // 2. Wrap Blade raw echos
                                const rawEchoRegex = new RegExp("\\{!!" + "[\\s\\S]*?" + "!!\\}", "g");
                                text = text.replace(rawEchoRegex, '<span class="blade-tag">$&</span>');
                                
                                // 3. Wrap Blade triple echos
                                const tripleEchoRegex = new RegExp("\\{\\{\\{" + "[\\s\\S]*?" + "\\}\\}", "g");
                                text = text.replace(tripleEchoRegex, '<span class="blade-tag">$&</span>');
                                
                                // 4. Wrap Blade standard echos
                                const standardEchoRegex = new RegExp("\\{\\{" + "[\\s\\S]*?" + "\\}\\}", "g");
                                text = text.replace(standardEchoRegex, '<span class="blade-tag">$&</span>');
                                
                                // 5. Wrap Blade directives with support for nested parenthesis up to 3 levels:
                                // e.g. foreach, endforeach, if, endif, elseif
                                const directiveRegex = new RegExp(
                                    "(?:^|([^a-zA-Z0-9_]))" +
                                    "@" +
                                    "(foreach|endforeach|if|endif|else|elseif|unless|endunless|for|endfor|while|endwhile|break|continue|php|endphp|include|includeIf|includeWhen|includeUnless|each|extend|extends|section|endsection|yield|stack|push|endpush|prepend|endprepend|auth|endauth|guest|endguest|env|endenv|hasSection|sectionMissing|switch|case|default|endswitch|class|style|error|enderror|csrf|method|vite|livewire|role|endrole|permission|endpermission|hasrole|hasanyrole|hasallroles)" +
                                    "(?:\\((?:[^()]+|\\((?:[^()]+|\\([^()]*\\))*\\))*\\))?",
                                    "gi"
                                );
                                
                                text = text.replace(directiveRegex, function(match, p1) {
                                    const prefix = p1 || '';
                                    const directive = match.slice(prefix.length);
                                    return prefix + '<span class="blade-tag">' + directive + '</span>';
                                });
                                
                                parts[i] = text;
                            }
                        }
                        
                        return parts.join('');
                    }
                    
                    // Helper to strip visual wrappers before saving
                    function unwrapBladeTags(html) {
                        if (!html) return '';
                        // Extremely robust unwrap that matches class containing "blade-tag" with single/double quotes and other attributes
                        return html.replace(/<span[^>]*class=['"][^'"]*blade-tag[^'"]*['"][^>]*>([\s\S]*?)<\/span>/gi, '$1');
                    }
                    
                    // Helper to clean/restore the content element and prevent Quill residues
                    function getCleanContentElement() {
                        let el = document.getElementById('content');
                        if (!el) return null;
                        
                        if (el.classList.contains('ql-container') || el.hasAttribute('contenteditable')) {
                            // Remove any existing Quill toolbars in the DOM
                            const toolbars = document.querySelectorAll('.ql-toolbar');
                            toolbars.forEach(tb => tb.remove());
                            quillInstance = null;
                            
                            const freshDiv = document.createElement('div');
                            freshDiv.id = 'content';
                            freshDiv.style.minHeight = '300px';
                            freshDiv.style.border = 'none';
                            
                            el.parentNode.replaceChild(freshDiv, el);
                            el = freshDiv;
                        }
                        contentElement = el;
                        return el;
                    }
                    
                    function initVisualEditor(content, direction = 'ltr') {
                        const el = getCleanContentElement();
                        if (!el) return;
                        
                        if (isComplex(content)) {
                            // Native contenteditable mode - preserves custom HTML 100% losslessly
                            if (quillInstance) {
                                const toolbar = document.querySelector('.ql-toolbar');
                                if (toolbar) toolbar.remove();
                                quillInstance = null;
                            }
                            
                            el.innerHTML = wrapBladeTags(content);
                            el.setAttribute('contenteditable', 'true');
                            el.setAttribute('dir', direction);
                            el.style.padding = '1.5rem';
                            el.style.outline = 'none';
                            el.style.minHeight = '300px';
                            el.style.backgroundColor = '#ffffff';
                            el.style.overflowY = 'auto';
                            
                            // Clean classes
                            el.className = 'form-control border-0';
                            
                            // Bind input listener to sync changes back to code editor
                            el.addEventListener('input', function () {
                                if (currentMode !== 'visual') return;
                                const html = unwrapBladeTags(el.innerHTML);
                                codeEditor.value = html;
                            });

                            // Strip rich-text formatting on paste to prevent font/background color corruption
                            el.addEventListener('paste', function (e) {
                                e.preventDefault();
                                const text = (e.originalEvent || e).clipboardData.getData('text/plain');
                                document.execCommand('insertText', false, text);
                            });
                        } else {
                            // Quill rich-text mode - standard rich-text experience
                            el.removeAttribute('contenteditable');
                            
                            quillInstance = new Quill('#content', { theme: 'snow' });
                            quillInstance.root.innerHTML = content;
                            quillInstance.root.setAttribute('dir', direction);
                            
                            quillInstance.on('text-change', function () {
                                if (currentMode !== 'visual') return;
                                const htmlContent = quillInstance.root.innerHTML;
                                codeEditor.value = htmlContent;
                            });
                        }
                    }
                    
                    function switchToVisual() {
                        if (isComplex(codeEditor.value)) {
                            if (!confirm("Warning: Switching to Visual mode on custom HTML layouts, templates, or forms may corrupt your custom code and Blade loops. Do you want to proceed?")) {
                                return;
                            }
                        }
                        if (currentMode === 'visual') return;
                        currentMode = 'visual';
                        
                        // Sync code textarea back to visual
                        initVisualEditor(codeEditor.value, quillInstance ? quillInstance.root.getAttribute('dir') : '{{ $currentDir }}');
                        
                        // Update buttons
                        btnVisual.classList.remove('btn-outline-secondary');
                        btnVisual.classList.add('btn-primary');
                        btnCode.classList.remove('btn-primary');
                        btnCode.classList.add('btn-outline-secondary');
                        
                        // Show / Hide containers
                        visualContainer.classList.remove('d-none');
                        codeContainer.classList.add('d-none');
                    }
                    
                    function switchToCode() {
                        if (currentMode === 'code') return;
                        currentMode = 'code';
                        
                        // Sync visual editor back to code textarea
                        const visualContent = quillInstance ? quillInstance.root.innerHTML : unwrapBladeTags(contentElement.innerHTML);
                        codeEditor.value = visualContent;
                        
                        // Update buttons
                        btnCode.classList.remove('btn-outline-secondary');
                        btnCode.classList.add('btn-primary');
                        btnVisual.classList.remove('btn-primary');
                        btnVisual.classList.add('btn-outline-secondary');
                        
                        // Show / Hide containers
                        visualContainer.classList.add('d-none');
                        codeContainer.classList.remove('d-none');
                    }
                    
                    btnVisual.addEventListener('click', switchToVisual);
                    btnCode.addEventListener('click', switchToCode);
                    
                    // Handle language switcher atomically via global event delegation (resilient to Livewire DOM re-renders)
                    document.addEventListener('change', function (e) {
                        if (e.target && e.target.id === 'language-switcher') {
                            const newLocale = e.target.value;
                            
                            let currentContent = '';
                            if (currentMode === 'visual') {
                                currentContent = quillInstance ? quillInstance.root.innerHTML : unwrapBladeTags(contentElement.innerHTML);
                            } else {
                                currentContent = codeEditor.value;
                            }
                            
                            if (currentContent === '<p><br></p>' || currentContent === '<p>&nbsp;</p>') {
                                currentContent = '';
                            }
                            
                            @this.switchLanguage(newLocale, currentContent);
                        }
                    });

                    // Sync content to Livewire locally before form submission via global event delegation
                    document.addEventListener('submit', function (e) {
                        const switcher = document.getElementById('language-switcher');
                        if (switcher) {
                            let currentContent = '';
                            if (currentMode === 'visual') {
                                currentContent = quillInstance ? quillInstance.root.innerHTML : unwrapBladeTags(contentElement.innerHTML);
                            } else {
                                currentContent = codeEditor.value;
                            }
                            
                            if (currentContent === '<p><br></p>' || currentContent === '<p>&nbsp;</p>') {
                                currentContent = '';
                            }
                            
                            const currentLocale = @this.get('activeLocale');
                            // Set property locally on client side to bundle with the submit request
                            @this.set('page.content.' + currentLocale, currentContent, false);
                        }
                    });
                    
                    // Initialize mode & visual editor
                    currentMode = isComplex(initialContent) ? 'code' : 'visual';
                    codeEditor.value = initialContent;
                    initVisualEditor(initialContent, '{{ $currentDir }}');
                    
                    if (currentMode === 'code') {
                        btnCode.classList.remove('btn-outline-secondary');
                        btnCode.classList.add('btn-primary');
                        btnVisual.classList.remove('btn-primary');
                        btnVisual.classList.add('btn-outline-secondary');
                        visualContainer.classList.add('d-none');
                        codeContainer.classList.remove('d-none');
                    } else {
                        btnVisual.classList.remove('btn-outline-secondary');
                        btnVisual.classList.add('btn-primary');
                        btnCode.classList.remove('btn-primary');
                        btnCode.classList.add('btn-outline-secondary');
                        visualContainer.classList.remove('d-none');
                        codeContainer.classList.add('d-none');
                    }
                    
                    // Listen for language switcher updates
                    window.addEventListener('contentLocaleChanged', event => {
                        const detail = (Array.isArray(event.detail) && event.detail.length > 0)
                            ? event.detail[0]
                            : (event.detail || {});
                        
                        const newContent = detail.content || '';
                        const direction = detail.direction || 'ltr';
                        
                        // Sync textarea
                        codeEditor.value = newContent;
                        
                        // Re-initialize visual editor with the new locale content
                        initVisualEditor(newContent, direction);
                    });
                }
            }, 300);
        });
    </script>
    @endpush
</div>
