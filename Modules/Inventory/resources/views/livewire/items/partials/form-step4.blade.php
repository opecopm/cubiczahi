<form wire:submit.prevent="saveStep4">
    <style>
        .accordion-button {
           font-size: 12px;
        }
    </style>
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Description</h4>
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

                        @if ($second_lang !== 'en')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-description-{{ $second_lang }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-description-{{ $second_lang }}" aria-expanded="false"
                                        aria-controls="collapse-description-{{ $second_lang }}">
                                        Description ({{ strtoupper($second_lang) }})
                                    </button>
                                </h2>
                                <div id="collapse-description-{{ $second_lang }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-description-{{ $second_lang }}"
                                    data-bs-parent="#itemDescriptionAccordion">
                                    <div class="accordion-body pt-3">
                                        <input type="hidden" id="wire-description-{{ $second_lang }}" wire:model.defer="descriptions.{{ $second_lang }}">
                                        <div wire:ignore>
                                            <textarea id="tiny-description-{{ $second_lang }}">{!! $descriptions[$second_lang] ?? '' !!}</textarea>
                                        </div>
                                        @error('descriptions.' . $second_lang)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

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

                        @if ($second_lang !== 'en')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-short-{{ $second_lang }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-short-{{ $second_lang }}" aria-expanded="false"
                                        aria-controls="collapse-short-{{ $second_lang }}">
                                        Short Description ({{ strtoupper($second_lang) }})
                                    </button>
                                </h2>
                                <div id="collapse-short-{{ $second_lang }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading-short-{{ $second_lang }}"
                                    data-bs-parent="#itemDescriptionAccordion">
                                    <div class="accordion-body pt-3">
                                        <input type="hidden" id="wire-short-{{ $second_lang }}" wire:model.defer="short_descriptions.{{ $second_lang }}">
                                        <div wire:ignore>
                                            <textarea id="tiny-short-{{ $second_lang }}">{!! $short_descriptions[$second_lang] ?? '' !!}</textarea>
                                        </div>
                                        @error('short_descriptions.' . $second_lang)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEO</h4>
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
                        @if ($second_lang !== 'en')
                            <div class="col-md-6">
                                <label class="form-label">Meta Title ({{ strtoupper($second_lang) }})</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="seo_title.{{ $second_lang }}">
                                @error('seo_title.' . $second_lang)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Meta Description (EN)</label>
                            <textarea rows="3" class="form-control" wire:model.defer="seo_description.en"></textarea>
                            @error('seo_description.en')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @if ($second_lang !== 'en')
                            <div class="col-md-6">
                                <label class="form-label">Meta Description ({{ strtoupper($second_lang) }})</label>
                                <textarea rows="3" class="form-control" wire:model.defer="seo_description.{{ $second_lang }}"></textarea>
                                @error('seo_description.' . $second_lang)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords (EN)</label>
                            <textarea rows="2" class="form-control" wire:model.defer="seo_keywords.en"></textarea>
                            @error('seo_keywords.en')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @if ($second_lang !== 'en')
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords ({{ strtoupper($second_lang) }})</label>
                                <textarea rows="2" class="form-control" wire:model.defer="seo_keywords.{{ $second_lang }}"></textarea>
                                @error('seo_keywords.' . $second_lang)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
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
