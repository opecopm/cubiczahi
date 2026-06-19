<form wire:submit.prevent="saveStep4">
    <style>
        .accordion-button {
           font-size: 12px;
        }
    </style>
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Description</h4>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#aiGenerateModal">
                            <i class="ti ti-sparkles me-1"></i>
                            Generate with AI
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" wire:click="autoTranslateDescriptions" wire:loading.attr="disabled">
                            <span wire:loading wire:target="autoTranslateDescriptions" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            <i class="ti ti-language me-1" wire:loading.remove wire:target="autoTranslateDescriptions"></i>
                            Auto-Translate Empty
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="accordion" id="itemDescriptionAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-description-en">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-description-en" aria-expanded="true"
                                    aria-controls="collapse-description-en">
                                    Description (EN)
                                </button>
                            </h2>
                            <div id="collapse-description-en" class="accordion-collapse collapse show"
                                aria-labelledby="heading-description-en" data-bs-parent="#itemDescriptionAccordion">
                                <div class="accordion-body pt-3">
                                    <input type="hidden" id="wire-description-en" wire:model.defer="descriptions.en">
                                    <div wire:ignore>
                                        <textarea id="tiny-description-en">{!! $descriptions['en'] ?? '' !!}</textarea>
                                    </div>
                                    @error('descriptions.en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @foreach($active_languages as $lang)
                        @if ($lang !== 'en')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-description-{{ $lang }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-description-{{ $lang }}" aria-expanded="false"
                                        aria-controls="collapse-description-{{ $lang }}">
                                        Description ({{ strtoupper($lang) }})
                                    </button>
                                </h2>
                                <div id="collapse-description-{{ $lang }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-description-{{ $lang }}"
                                    data-bs-parent="#itemDescriptionAccordion">
                                    <div class="accordion-body pt-3">
                                        <input type="hidden" id="wire-description-{{ $lang }}" wire:model.defer="descriptions.{{ $lang }}">
                                        <div wire:ignore>
                                            <textarea id="tiny-description-{{ $lang }}">{!! $descriptions[$lang] ?? '' !!}</textarea>
                                        </div>
                                        @error('descriptions.' . $lang)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                        @endforeach

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-short-en">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-short-en" aria-expanded="false"
                                    aria-controls="collapse-short-en">
                                    Short Description (EN)
                                </button>
                            </h2>
                            <div id="collapse-short-en" class="accordion-collapse collapse"
                                aria-labelledby="heading-short-en" data-bs-parent="#itemDescriptionAccordion">
                                <div class="accordion-body pt-3">
                                    <input type="hidden" id="wire-short-en" wire:model.defer="short_descriptions.en">
                                    <div wire:ignore>
                                        <textarea id="tiny-short-en">{!! $short_descriptions['en'] ?? '' !!}</textarea>
                                    </div>
                                    @error('short_descriptions.en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @foreach($active_languages as $lang)
                        @if ($lang !== 'en')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-short-{{ $lang }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-short-{{ $lang }}" aria-expanded="false"
                                        aria-controls="collapse-short-{{ $lang }}">
                                        Short Description ({{ strtoupper($lang) }})
                                    </button>
                                </h2>
                                <div id="collapse-short-{{ $lang }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-short-{{ $lang }}"
                                    data-bs-parent="#itemDescriptionAccordion">
                                    <div class="accordion-body pt-3">
                                        <input type="hidden" id="wire-short-{{ $lang }}" wire:model.defer="short_descriptions.{{ $lang }}">
                                        <div wire:ignore>
                                            <textarea id="tiny-short-{{ $lang }}">{!! $short_descriptions[$lang] ?? '' !!}</textarea>
                                        </div>
                                        @error('short_descriptions.' . $lang)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">SEO</h4>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-warning me-2" wire:click="generateSeoTags" wire:loading.attr="disabled">
                            <span wire:loading wire:target="generateSeoTags" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            <i class="ti ti-sparkles me-1" wire:loading.remove wire:target="generateSeoTags"></i>
                            Generate with AI
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" wire:click="autoTranslateSeo" wire:loading.attr="disabled">
                            <span wire:loading wire:target="autoTranslateSeo" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            <i class="ti ti-language me-1" wire:loading.remove wire:target="autoTranslateSeo"></i>
                            Auto-Translate Empty
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title (EN)</label>
                            <input type="text" class="form-control" wire:model.defer="seo_title.en">
                            @error('seo_title.en')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @foreach($active_languages as $lang)
                        @if ($lang !== 'en')
                            <div class="col-md-6">
                                <label class="form-label">Meta Title ({{ strtoupper($lang) }})</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="seo_title.{{ $lang }}">
                                @error('seo_title.' . $lang)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        @endforeach

                        <div class="col-md-6">
                            <label class="form-label">Meta Description (EN)</label>
                            <textarea rows="3" class="form-control" wire:model.defer="seo_description.en"></textarea>
                            @error('seo_description.en')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @foreach($active_languages as $lang)
                        @if ($lang !== 'en')
                            <div class="col-md-6">
                                <label class="form-label">Meta Description ({{ strtoupper($lang) }})</label>
                                <textarea rows="3" class="form-control" wire:model.defer="seo_description.{{ $lang }}"></textarea>
                                @error('seo_description.' . $lang)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        @endforeach

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords (EN)</label>
                            <textarea rows="2" class="form-control" wire:model.defer="seo_keywords.en"></textarea>
                            @error('seo_keywords.en')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @foreach($active_languages as $lang)
                        @if ($lang !== 'en')
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords ({{ strtoupper($lang) }})</label>
                                <textarea rows="2" class="form-control" wire:model.defer="seo_keywords.{{ $lang }}"></textarea>
                                @error('seo_keywords.' . $lang)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(3)">Back</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>
