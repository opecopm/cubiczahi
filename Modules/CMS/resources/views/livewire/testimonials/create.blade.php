@php
    $activeDirection = $activeLanguages->where('code', $activeLocale)->first()?->direction ?? 'ltr';
@endphp
<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Add New Testimonial</h2>
                </div>
                <div class="col-auto ms-auto d-flex align-items-center gap-2">
                    <select class="form-select border-secondary text-secondary fw-semibold" wire:model.live="activeLocale" style="width: auto; height: 36px; padding-top: 4px; padding-bottom: 4px;">
                        @foreach ($activeLanguages as $lang)
                            <option value="{{ $lang->code }}">
                                🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.cms.testimonials.index') }}" class="btn btn-outline-secondary" style="height: 36px; display: inline-flex; align-items: center;">
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

                    <!-- Left Column (Testimonial Details - Dynamic) -->
                    <div class="col-lg-8" wire:key="testimonial-details-{{ $activeLocale }}">
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h3 class="card-title">Testimonial Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-primary">Name ({{ strtoupper($activeLocale) }}) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('translations.' . $activeLocale . '.name') is-invalid @enderror" dir="{{ $activeDirection }}"
                                               placeholder="Enter name" wire:model.defer="translations.{{ $activeLocale }}.name">
                                        @error('translations.' . $activeLocale . '.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-primary">Designation ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('translations.' . $activeLocale . '.designation') is-invalid @enderror" dir="{{ $activeDirection }}"
                                               placeholder="Enter designation" wire:model.defer="translations.{{ $activeLocale }}.designation">
                                        @error('translations.' . $activeLocale . '.designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Company ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('translations.' . $activeLocale . '.company') is-invalid @enderror" dir="{{ $activeDirection }}"
                                               placeholder="Enter company name" wire:model.defer="translations.{{ $activeLocale }}.company">
                                        @error('translations.' . $activeLocale . '.company') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Phone ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('translations.' . $activeLocale . '.phone') is-invalid @enderror" dir="{{ $activeDirection }}"
                                               placeholder="Enter phone number" wire:model.defer="translations.{{ $activeLocale }}.phone">
                                        @error('translations.' . $activeLocale . '.phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Location ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" class="form-control @error('translations.' . $activeLocale . '.location') is-invalid @enderror" dir="{{ $activeDirection }}"
                                               placeholder="Enter location" wire:model.defer="translations.{{ $activeLocale }}.location">
                                        @error('translations.' . $activeLocale . '.location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Website ({{ strtoupper($activeLocale) }})</label>
                                        <input type="url" class="form-control @error('translations.' . $activeLocale . '.website') is-invalid @enderror" dir="{{ $activeDirection }}"
                                               placeholder="Enter website URL" wire:model.defer="translations.{{ $activeLocale }}.website">
                                        @error('translations.' . $activeLocale . '.website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">About ({{ strtoupper($activeLocale) }})</label>
                                        <textarea class="form-control @error('translations.' . $activeLocale . '.about') is-invalid @enderror" rows="3" dir="{{ $activeDirection }}"
                                                  placeholder="Write about the person..." wire:model.defer="translations.{{ $activeLocale }}.about"></textarea>
                                        @error('translations.' . $activeLocale . '.about') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold text-primary">Message ({{ strtoupper($activeLocale) }}) <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('translations.' . $activeLocale . '.message') is-invalid @enderror" rows="4" dir="{{ $activeDirection }}"
                                                  placeholder="Write testimonial message..." wire:model.defer="translations.{{ $activeLocale }}.message"></textarea>
                                        @error('translations.' . $activeLocale . '.message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (Sidebar & Non-Translatable Details) -->
                    <div class="col-lg-4">
                        
                        {{-- Save Card --}}
                        <div class="card mb-3">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="ti ti-device-floppy me-1"></i> Save Testimonial
                                </button>
                            </div>
                        </div>

                        {{-- Image Upload Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Profile Image</h3>
                            </div>
                            <div class="card-body">
                                <input type="file" class="form-control @error('image') is-invalid @enderror" wire:model="image" accept="image/*">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                        wire:click="$dispatch('openMediaPicker', { allowedTypes: ['image'], multiple: false, usage: 'testimonial-image' })">
                                        <i class="ti ti-photo me-1"></i> Choose from Media Gallery
                                    </button>
                                </div>
                                <div class="form-hint">Max 2MB. SVG, PNG, JPG, WebP.</div>
                                @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                @if($image)
                                    <div class="mt-3 text-center border p-2 rounded bg-light">
                                        @if($image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="rounded border shadow-sm" style="max-height:150px; max-width: 100%;">
                                        @else
                                            <img src="{{ asset('storage/' . $image) }}" alt="Preview" class="rounded border shadow-sm" style="max-height:150px; max-width: 100%;">
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Video Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Video Feature</h3>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs card-header-tabs mb-3" id="videoTabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#video-link-pane" type="button" role="tab">
                                            <i class="ti ti-link me-1"></i> Link
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#video-upload-pane" type="button" role="tab">
                                            <i class="ti ti-upload me-1"></i> Upload File
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content border border-top-0 rounded-bottom p-3">
                                    
                                    {{-- Video URL Tab --}}
                                    <div class="tab-pane fade show active" id="video-link-pane" role="tabpanel">
                                        <label class="form-label">Video URL (YouTube / Vimeo)</label>
                                        <input type="text" class="form-control @error('video_url') is-invalid @enderror" 
                                               placeholder="https://www.youtube.com/watch?v=..." wire:model.defer="video_url">
                                        @error('video_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        
                                        @if($video_url)
                                            <div class="mt-3">
                                                <iframe class="img-fluid rounded border shadow-sm" style="height:150px;width:100%" 
                                                        src="{{ str_replace('watch?v=', 'embed/', $video_url) }}" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Video File Tab --}}
                                    <div class="tab-pane fade" id="video-upload-pane" role="tabpanel">
                                        <label class="form-label">Upload Video File</label>
                                        <input type="file" class="form-control @error('video_file') is-invalid @enderror" accept="video/*" wire:model="video_file">
                                        <div class="form-hint">Max 50MB. MP4, MOV, AVI.</div>
                                        @error('video_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        
                                        @if($video_file)
                                            <div class="mt-3 text-center border p-2 rounded bg-light">
                                                <video class="img-fluid rounded" style="max-height:150px;" controls>
                                                    <source src="{{ $video_file->temporaryUrl() }}">
                                                </video>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Settings Card --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <select class="form-select @error('rating') is-invalid @enderror" wire:model.defer="rating">
                                        <option value="0">— No Rating —</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="5">5 Stars</option>
                                    </select>
                                    @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           placeholder="0" wire:model.defer="sort_order">
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch mt-2">
                                        <input class="form-check-input @error('featured') is-invalid @enderror" type="checkbox" wire:model.defer="featured" id="featured">
                                        <span class="form-check-label fw-bold">Featured Testimonial</span>
                                    </label>
                                    @error('featured') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input @error('status') is-invalid @enderror" type="checkbox" wire:model.defer="status" id="status">
                                        <span class="form-check-label fw-bold text-success">Active / Visible</span>
                                    </label>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    @livewire('mediagallery::media-picker', ['showButton' => false, 'multiple' => false, 'allowedTypes' => ['image']])
</div>
