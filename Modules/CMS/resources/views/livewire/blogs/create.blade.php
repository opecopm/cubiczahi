<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Add New Blog</h2>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" wire:click="createBlog" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Publish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <form id="blogCreateForm" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <!-- Language Tabs -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($languages as $locale => $label)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if($loop->first) active @endif" id="tab-{{ $locale }}"
                                                data-bs-toggle="tab" data-bs-target="#content-{{ $locale }}" type="button"
                                                role="tab">
                                                {{ $label }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($languages as $locale => $label)
                                        <div class="tab-pane fade @if($loop->first) show active @endif"
                                            id="content-{{ $locale }}" role="tabpanel">

                                            <div class="mb-3">
                                                <input type="text" wire:model.defer="title.{{ $locale }}"
                                                    class="form-control fs-4 @error('title.' . $locale) is-invalid @enderror"
                                                    placeholder="Add title" required>
                                                @error('title.' . $locale)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3" wire:ignore>
                                                <label class="form-label fw-semibold">Content</label>
                                                <div id="editor-{{ $locale }}" class="bg-white" style="height: 500px;">
                                                    {!! $content[$locale] ?? '' !!}
                                                </div>
                                                @error('content.' . $locale)
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Excerpt</label>
                                                <textarea wire:model.defer="excerpt.{{ $locale }}"
                                                    class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Discussion -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Discussion</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="allow_comments">
                                        <label class="form-check-label">Allow comments</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="allow_pings">
                                        <label class="form-check-label">Allow trackbacks and pingbacks</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">

                        <!-- Publish Box -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Publish</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label">Status</label>
                                    <select wire:model.defer="status" class="form-select">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Author Information -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Author Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Author Name</label>
                                    <input type="text" wire:model.defer="author_name" class="form-control" placeholder="Enter author name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Author Image URL</label>
                                    <input type="text" wire:model.defer="author_image" class="form-control" placeholder="Author image path (e.g., blogs/authors/john.jpg)">
                                    <div class="form-text text-muted">Enter the image path from storage (e.g., blogs/authors/image.jpg)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Publishing Details -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Publishing Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Published At</label>
                                    <input type="datetime-local" wire:model.defer="published_at" class="form-control">
                                    <div class="form-text text-muted">Leave empty to use current date when published</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Comments Count</label>
                                    <input type="number" wire:model.defer="comments_count" class="form-control" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Categories</h3>
                                <div class="card-options">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#addCategoryModal">
                                        + Add New
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach($categories as $category)
                                    <div class="form-check ms-0">
                                        <input type="checkbox" value="{{ $category->id }}" wire:model.defer="categoryIds"
                                            class="form-check-input" id="cat{{ $category->id }}">
                                        <label class="form-check-label"
                                            for="cat{{ $category->id }}">{{ $category->getTranslation('name', app()->getLocale()) ?? $category->getTranslation('name', app()->getLocale()) }}</label>
                                    </div>

                                    @if($category->children && $category->children->count())
                                        @foreach($category->children as $sub)
                                            <div class="form-check ms-4">
                                                <input type="checkbox" value="{{ $sub->id }}" wire:model.defer="categoryIds"
                                                    class="form-check-input" id="subcat{{ $sub->id }}">
                                                <label class="form-check-label" for="subcat{{ $sub->id }}">{{ $sub->name }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Tags</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($tags as $i => $tag)
                                        <span class="badge bg-primary">
                                            {{ $tag }}
                                            <i class="ti ti-x ms-1 cursor-pointer" wire:click="removeTag({{ $i }})"></i>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="input-group">
                                    <input type="text" wire:model.defer="newTag" class="form-control"
                                        placeholder="Add new tag">
                                    <button type="button" wire:click="addTag"
                                        class="btn btn-outline-primary">Add</button>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="card mb-3" x-data="{ imagePreview: null }">
                            <div class="card-header">
                                <h3 class="card-title">Featured Image</h3>
                            </div>
                            <div class="card-body text-center">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" class="img-fluid mb-2 rounded">
                                </template>
                                <input type="file" wire:model="image" class="form-control mb-2" @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = e => imagePreview = e.target.result;
                                        reader.readAsDataURL(file);
                                    }
                                ">
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div wire:ignore.self class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-primary me-2" wire:click="toggleParent">Add Parent</button>
                        <button type="button" class="btn btn-outline-primary" wire:click="toggleChild">Add Subcategory</button>
                    </div>

                    <form wire:submit.prevent="saveCategory">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" class="form-control @error('cat_name') is-invalid @enderror"
                                wire:model.defer="cat_name" placeholder="Enter category name">
                            @error('cat_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($isChild)
                            <div class="mb-3">
                                <label class="form-label">Select Parent Category</label>
                                <select class="form-select @error('parentId') is-invalid @enderror" wire:model.defer="parentId">
                                    <option value="">-- Select Parent --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->getTranslation('name', app()->getLocale()) ?? $category->getTranslation('name', app()->getLocale()) }}</option>
                                    @endforeach
                                </select>
                                @error('parentId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control @error('cat_slug') is-invalid @enderror"
                                wire:model.defer="cat_slug" placeholder="Enter slug">
                            @error('cat_slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="catStatus" wire:model.defer="cat_status">
                            <label class="form-check-label" for="catStatus">Active</label>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            const id = event.detail.id;
            const el = document.getElementById(id);
            if (!el) return;
            const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            modal.hide();
        });
    </script>

    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const languages = @json(array_keys($languages));

            languages.forEach(locale => {
                const editorId = 'editor-' + locale;
                const container = document.getElementById(editorId);

                if (container) {
                    const quill = new Quill('#' + editorId, {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                ['blockquote', 'code-block'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'script': 'sub'}, { 'script': 'super' }],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                [{ 'direction': 'rtl' }],
                                [{ 'size': ['small', false, 'large', 'huge'] }],
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'font': [] }],
                                [{ 'align': [] }],
                                ['clean'],
                                ['link', 'image', 'video']
                            ]
                        }
                    });

                    quill.on('text-change', function(delta, oldDelta, source) {
                        @this.set('content.' + locale, quill.root.innerHTML);
                    });
                }
            });
        });
    </script>
</div>
