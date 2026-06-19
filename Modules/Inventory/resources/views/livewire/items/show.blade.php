<div>
    @php
        $page_name = ucwords(str_replace('_', ' ', $item->type ?? ''));
        $items_url = route('admin.inventory.items.index');
        if ($item->type == 'service') {
            $items_url = route('admin.inventory.services');
        }
        if ($item->type == 'spare_part') {
            $items_url = route('admin.inventory.items.index');
        }
    @endphp

    @component('admin.partials.page.inner-header', [
        'title' => $page_name . ' #' . ($item->reference ?? $item->id),
        'breadcrumbs' => [
            [
                'label' => $page_name ?: 'Items',
                'url' => $items_url,
                'icon' => 'back',
            ],
            [
                'label' => $item->reference ?? $item->id,
                'active' => true,
            ],
        ],
    ])
        @slot('actions')
            <div class="btn-list">
                <a href="{{ route('admin.inventory.items.edit', $item) }}" class="btn btn-primary d-print-none">
                    <i class="ti ti-edit me-1"></i>
                    Edit
                </a>
                <button type="button" wire:click="openPriceModal" class="btn btn-outline-primary d-print-none">
                    <i class="ti ti-plus me-1"></i>
                    Add Price
                </button>
                <button type="button" wire:click="openSearchModal" class="btn btn-outline-secondary d-print-none">
                    <i class="ti ti-search me-1"></i>
                    Search
                </button>
                <a href="{{ $items_url }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>
                    Back
                </a>
            </div>
        @endslot
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="row mt-3">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header p-3">
                                    <h5 class="mb-0">{{ $item->name ?? '' }}</h5>
                                </div>
                                <div class="card-body p-3">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <span
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Type</span>
                                            <span
                                                class="text-sm font-weight-bold">{{ \Modules\Inventory\Models\Item::TYPE_SELECT[$item->type] ?? ($item->type ?? '-') }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Status</span>
                                            <span
                                                class="text-sm font-weight-bold">{{ $item->status_label ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Category</span>
                                            <span
                                                class="text-sm font-weight-bold">{{ $item->category->name ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Brand</span>
                                            <span
                                                class="text-sm font-weight-bold">{{ $item->brand->name ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Model
                                                Number</span>
                                            <span
                                                class="text-sm font-weight-bold text-uppercase">{{ $item->model_number ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Warranty
                                                (Months)</span>
                                            <span
                                                class="text-sm font-weight-bold">{{ $item->warranty_months ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header p-3">
                                    <h5 class="mb-0">Descriptions</h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="accordion" id="itemShowDescriptionAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-show-description-en">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-show-description-en" aria-expanded="true"
                                                    aria-controls="collapse-show-description-en">
                                                    Description (EN)
                                                </button>
                                            </h2>
                                            <div id="collapse-show-description-en"
                                                class="accordion-collapse collapse show"
                                                aria-labelledby="heading-show-description-en"
                                                data-bs-parent="#itemShowDescriptionAccordion">
                                                <div class="accordion-body">
                                                    @if (!empty($descriptions['en']))
                                                        {!! $descriptions['en'] !!}
                                                    @else
                                                        <div class="text-muted">—</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if ($second_lang !== 'en')
                                            <div class="accordion-item">
                                                <h2 class="accordion-header"
                                                    id="heading-show-description-{{ $second_lang }}">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-show-description-{{ $second_lang }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse-show-description-{{ $second_lang }}">
                                                        Description ({{ strtoupper($second_lang) }})
                                                    </button>
                                                </h2>
                                                <div id="collapse-show-description-{{ $second_lang }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="heading-show-description-{{ $second_lang }}"
                                                    data-bs-parent="#itemShowDescriptionAccordion">
                                                    <div class="accordion-body">
                                                        @if (!empty($descriptions[$second_lang]))
                                                            {!! $descriptions[$second_lang] !!}
                                                        @else
                                                            <div class="text-muted">—</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-show-short-en">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse-show-short-en"
                                                    aria-expanded="false" aria-controls="collapse-show-short-en">
                                                    Short Description (EN)
                                                </button>
                                            </h2>
                                            <div id="collapse-show-short-en" class="accordion-collapse collapse"
                                                aria-labelledby="heading-show-short-en"
                                                data-bs-parent="#itemShowDescriptionAccordion">
                                                <div class="accordion-body">
                                                    @if (!empty($short_descriptions['en']))
                                                        {!! $short_descriptions['en'] !!}
                                                    @else
                                                        <div class="text-muted">—</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if ($second_lang !== 'en')
                                            <div class="accordion-item">
                                                <h2 class="accordion-header"
                                                    id="heading-show-short-{{ $second_lang }}">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-show-short-{{ $second_lang }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse-show-short-{{ $second_lang }}">
                                                        Short Description ({{ strtoupper($second_lang) }})
                                                    </button>
                                                </h2>
                                                <div id="collapse-show-short-{{ $second_lang }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="heading-show-short-{{ $second_lang }}"
                                                    data-bs-parent="#itemShowDescriptionAccordion">
                                                    <div class="accordion-body">
                                                        @if (!empty($short_descriptions[$second_lang]))
                                                            {!! $short_descriptions[$second_lang] !!}
                                                        @else
                                                            <div class="text-muted">—</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header p-3">
                                    <h5 class="mb-0">SEO</h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block mb-1">
                                                Meta Title (EN)</div>
                                            <div class="text-sm">{{ $seo_title['en'] ?: '—' }}</div>
                                        </div>
                                        @if ($second_lang !== 'en')
                                            <div class="col-md-6">
                                                <div
                                                    class="text-xs text-uppercase text-secondary font-weight-bolder d-block mb-1">
                                                    Meta Title ({{ strtoupper($second_lang) }})</div>
                                                <div class="text-sm">{{ $seo_title[$second_lang] ?: '—' }}</div>
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block mb-1">
                                                Meta Description (EN)</div>
                                            <div class="text-sm">{{ $seo_description['en'] ?: '—' }}</div>
                                        </div>
                                        @if ($second_lang !== 'en')
                                            <div class="col-md-6">
                                                <div
                                                    class="text-xs text-uppercase text-secondary font-weight-bolder d-block mb-1">
                                                    Meta Description ({{ strtoupper($second_lang) }})</div>
                                                <div class="text-sm">{{ $seo_description[$second_lang] ?: '—' }}</div>
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div
                                                class="text-xs text-uppercase text-secondary font-weight-bolder d-block mb-1">
                                                Meta Keywords (EN)</div>
                                            <div class="text-sm">{{ $seo_keywords['en'] ?: '—' }}</div>
                                        </div>
                                        @if ($second_lang !== 'en')
                                            <div class="col-md-6">
                                                <div
                                                    class="text-xs text-uppercase text-secondary font-weight-bolder d-block mb-1">
                                                    Meta Keywords ({{ strtoupper($second_lang) }})</div>
                                                <div class="text-sm">{{ $seo_keywords[$second_lang] ?: '—' }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Prices</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary mb-0 d-print-none"
                                        wire:click="openPriceModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1"
                                            width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                        Add Price
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Party</th>
                                                    <th>Currency</th>
                                                    <th>Price</th>
                                                    <th>Valid From</th>
                                                    <th>Valid To</th>
                                                    <th>Default</th>
                                                    <th class="d-print-none text-end w-1">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item->prices->sortByDesc('date_from') as $price)
                                                    <tr>
                                                        <td>{{ \Modules\Inventory\Models\ItemPrice::PRICE_SELECT[$price->price_type] ?? ucfirst($price->price_type) }}
                                                        </td>
                                                        <td>
                                                            @if ($price->price_type === 'purchase')
                                                                -
                                                            @elseif ($price->price_type === 'selling')
                                                                {{ $price->customer->name ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $price->currency ?? '-' }}</td>
                                                        <td>{{ number_format($price->price, 2) }}</td>
                                                        <td>{{ $price->date_from ? \Carbon\Carbon::parse($price->date_from)->format('Y-m-d') : '-' }}
                                                        </td>
                                                        <td>{{ $price->date_to ? \Carbon\Carbon::parse($price->date_to)->format('Y-m-d') : '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($price->is_default)
                                                                <span class="badge bg-success-lt">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon icon-tabler me-1" width="14"
                                                                        height="14" viewBox="0 0 24 24"
                                                                        stroke-width="3" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        style="vertical-align: -2px;">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path d="M5 12l5 5l10 -10" />
                                                                    </svg>
                                                                    Default
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary-lt">No</span>
                                                            @endif
                                                        </td>
                                                        <td class="d-print-none text-end">
                                                            <div
                                                                class="d-inline-flex gap-2 flex-nowrap align-items-center">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    wire:click="openPriceModal({{ $price->id }})"
                                                                    aria-label="Edit price">
                                                                    <i class="ti ti-edit"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    wire:click="deletePrice({{ $price->id }})"
                                                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                                    aria-label="Delete price">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($item->prices->isEmpty())
                                                    <tr>
                                                        <td colspan="8" class="text-center text-secondary py-4">No
                                                            price records found.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if ($item->type === 'product' || $item->type === 'spare_part')
                                <div class="card mb-4">
                                    @if ($item->type === 'product')
                                        <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Spare Parts</h5>
                                            <div class="btn-list d-print-none">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="openSparePartsModal">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler me-1" width="16" height="16"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 15l6 -6" />
                                                        <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" />
                                                        <path
                                                            d="M13 18l-.397 .534a5 5 0 0 1 -7.071 -7.072l.534 -.464" />
                                                    </svg>
                                                    Link Spare Parts
                                                </button>
                                                <a class="btn btn-sm btn-primary"
                                                    href="{{ route('admin.inventory.items.create', ['type' => 'spare_part', 'product_id' => $item->id]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler me-1" width="16" height="16"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M12 5l0 14" />
                                                        <path d="M5 12l14 0" />
                                                    </svg>
                                                    New Spare Part
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Products</h5>
                                            <div class="btn-list d-print-none">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="openProductsModal">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler me-1" width="16" height="16"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 15l6 -6" />
                                                        <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" />
                                                        <path
                                                            d="M13 18l-.397 .534a5 5 0 0 1 -7.071 -7.072l.534 -.464" />
                                                    </svg>
                                                    Link Products
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-body p-3">
                                        <div class="table-responsive">
                                            <table class="table align-items-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Reference</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th class="text-end d-print-none">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $relatedItems = $item->type === 'product' ? $item->spareParts : $item->products; ?>

                                                    @forelse($relatedItems as $related)
                                                        <tr>
                                                            <td class="text-sm">
                                                                {{ $related->model_number ?? $related->id }}</td>
                                                            <td class="text-sm">
                                                                {{ $related->getTranslation('name', 'en') ?? $related->name }}
                                                            </td>
                                                            <td class="text-sm">
                                                                {{ \Modules\Inventory\Models\Item::TYPE_SELECT[$related->type] ?? ($related->type ?? '-') }}
                                                            </td>
                                                            <td class="text-end d-print-none">
                                                                <a class="btn btn-sm btn-outline-primary"
                                                                    href="{{ route('admin.inventory.items.show', $related->id) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon icon-tabler me-1" width="16"
                                                                        height="16" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                                        <path
                                                                            d="M21 12c-2.4 4 -5.4 6 -9 6s-6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6s6.6 2 9 6" />
                                                                    </svg>
                                                                    View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-sm text-muted">
                                                                @if ($item->type === 'product')
                                                                    No spare parts linked.
                                                                @else
                                                                    No products linked.
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body p-3 text-center">
                                    <div class="avatar avatar-xl position-relative">
                                        <img src="{{ $item->getFirstMediaUrl('primary_photo') ? $item->getFirstMediaUrl('primary_photo') : url('assets/img/no-photo.jpg') }}"
                                            alt="item_image" class="w-100 border-radius-lg shadow-sm">
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="mb-0">{{ $item->getTranslation('name', 'en') }}</h6>
                                        @php
                                            $langs = system_setting('active_languages', ['ar']);
                                            $active_languages = is_string($langs)
                                                ? json_decode($langs, true) ?? [$langs]
                                                : $langs;
                                        @endphp
                                        @foreach ($active_languages as $lang)
                                            @if ($lang !== 'en' && $item->getTranslation('name', $lang))
                                                <div class="text-sm text-muted">
                                                    {{ $item->getTranslation('name', $lang) }}</div>
                                            @endif
                                        @endforeach
                                        <div class="text-xs text-muted mt-1">{{ $item->reference }}</div>
                                    </div>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary mt-3 mb-0 d-print-none"
                                        wire:click="openPhotoModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1"
                                            width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 8h.01" />
                                            <path
                                                d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                            <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                            <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                        </svg>
                                        Update Photo
                                    </button>
                                </div>
                            </div>

                            <div class="card mb-4 d-print-none">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Manual</h6>

                                    @php($manual = $item->getFirstMedia('manual'))

                                    @if ($manual)
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Manual Name</th>
                                                        <th class="text-end">Uploaded</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-sm">{{ $manual->file_name }}</td>
                                                        <td class="text-sm text-end">
                                                            {{ $manual->created_at?->format('Y-m-d') ?? '-' }}</td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary"
                                                                href="{{ $manual->getUrl() }}" target="_blank"
                                                                rel="noopener">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler me-1" width="16"
                                                                    height="16" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor"
                                                                    fill="none" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path
                                                                        d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                                    <path d="M7 11l5 5l5 -5" />
                                                                    <path d="M12 4l0 12" />
                                                                </svg>
                                                                Download
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                wire:click="deleteManual"
                                                                onclick="confirm('Delete this manual?') || event.stopImmediatePropagation()">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler me-1" width="16"
                                                                    height="16" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor"
                                                                    fill="none" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M4 7l16 0" />
                                                                    <path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" />
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path
                                                                        d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-sm text-muted">No manual uploaded.</div>
                                    @endif

                                    <form wire:submit.prevent="uploadManual" class="mt-5">
                                        <input type="file" class="form-control" accept="application/pdf"
                                            wire:model="manual_file">
                                        @error('manual_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2 mb-0"
                                            wire:loading.attr="disabled">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1"
                                                width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 9l5 -5l5 5" />
                                                <path d="M12 4l0 12" />
                                            </svg>
                                            Upload Manual
                                        </button>
                                        <div class="text-xs text-muted mt-1" wire:loading wire:target="manual_file">
                                            Uploading...
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Overview</h6>
                                    <ul class="list-group">
                                        <li
                                            class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-success-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler"
                                                        width="16" height="16" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Status</h6>
                                                    <span class="text-xs">{{ $item->status_label ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li
                                            class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-info-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler"
                                                        width="16" height="16" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 4h6v6h-6z" />
                                                        <path d="M14 4h6v6h-6z" />
                                                        <path d="M4 14h6v6h-6z" />
                                                        <path d="M14 14h6v6h-6z" />
                                                    </svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Category</h6>
                                                    <span class="text-xs">{{ $item->category->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li
                                            class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-dark-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler"
                                                        width="16" height="16" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M12 3v19" />
                                                        <path d="M5 12l14 0" />
                                                    </svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Track Inventory</h6>
                                                    <span
                                                        class="text-xs text-muted">{{ $item->track_inventory ? 'Enabled' : 'Disabled' }}</span>
                                                </div>
                                            </div>
                                            <div class="ms-auto">
                                                <label class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox"
                                                        @checked($item->track_inventory) disabled>
                                                </label>
                                            </div>
                                        </li>
                                        <li
                                            class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-warning-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler"
                                                        width="16" height="16" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M7 7a5 5 0 0 1 10 0v4a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2v-4" />
                                                    </svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Serialized</h6>
                                                    <span
                                                        class="text-xs text-muted">{{ $item->is_serialized ? 'Enabled' : 'Disabled' }}</span>
                                                </div>
                                            </div>
                                            <div class="ms-auto">
                                                <label class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox"
                                                        @checked($item->is_serialized) disabled>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>

                                    <hr class="my-3">

                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Dates</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm">Created:</span>
                                        <span
                                            class="text-sm font-weight-bold">{{ $item->created_at?->format('M d, Y') ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm">Updated:</span>
                                        <span
                                            class="text-sm font-weight-bold">{{ $item->updated_at?->format('M d, Y') ?? '-' }}</span>
                                    </div>

                                    <hr class="my-3">

                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">People</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm">Created By:</span>
                                        <span
                                            class="text-sm font-weight-bold">{{ $item->createdBy->name ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm">Updated By:</span>
                                        <span
                                            class="text-sm font-weight-bold">{{ $item->updatedBy->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade @if ($searchModal) show d-block @endif" tabindex="-1"
                role="dialog" style="background: rgba(0,0,0,0.5);"
                @if ($searchModal) aria-modal="true" @else aria-hidden="true" @endif>
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Search Items</h5>
                            <button type="button" class="btn-close" wire:click="closeSearchModal"></button>
                        </div>
                        <div class="modal-body">
                            <label for="search-item-modal" class="form-label">Item</label>
                            <input type="text" class="form-control" id="search-item-modal"
                                placeholder="Type item name or reference..."
                                wire:model.live.debounce.500ms="itemSearch" autofocus>

                            @if (!empty($itemResults))
                                <div class="mt-3">
                                    <ul class="list-group">
                                        @foreach ($itemResults as $row)
                                            <li class="list-group-item list-group-item-action cursor-pointer d-flex justify-content-between align-items-center"
                                                wire:click="redirectToItem({{ $row->id }})">
                                                <span class="text-sm">{{ $row->reference }} —
                                                    {{ $row->name }}</span>
                                                <span class="text-muted">&rarr;</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(strlen($itemSearch) > 1)
                                <div class="text-sm text-muted mt-3">No results found.</div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto"
                                wire:click="closeSearchModal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade @if ($sparePartsModal) show d-block @endif" tabindex="-1"
                role="dialog" style="background: rgba(0,0,0,0.5);"
                @if ($sparePartsModal) aria-modal="true" @else aria-hidden="true" @endif>
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Link Spare Parts</h5>
                            <button type="button" class="btn-close" wire:click="closeSparePartsModal"></button>
                        </div>
                        <div class="modal-body">
                            <label for="spare-part-search" class="form-label">Spare Part</label>
                            <input type="text" class="form-control" id="spare-part-search"
                                placeholder="Type spare part name or reference..."
                                wire:model.live.debounce.500ms="sparePartSearch" autofocus>

                            @if (!empty($sparePartResults))
                                <div class="mt-3">
                                    <ul class="list-group">
                                        @foreach ($sparePartResults as $row)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="text-sm">
                                                    <div class="fw-bold">{{ $row->reference ?? $row->id }}</div>
                                                    <div class="text-muted">
                                                        {{ $row->getTranslation('name', 'en') ?? $row->name }}</div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-info mb-0"
                                                    wire:click="attachSparePart({{ $row->id }})">
                                                    Add
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(strlen($sparePartSearch) > 1)
                                <div class="text-sm text-muted mt-3">No results found.</div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto"
                                wire:click="closeSparePartsModal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade @if ($productsModal) show d-block @endif" tabindex="-1"
                role="dialog" style="background: rgba(0,0,0,0.5);"
                @if ($productsModal) aria-modal="true" @else aria-hidden="true" @endif>
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Link Products</h5>
                            <button type="button" class="btn-close" wire:click="closeProductsModal"></button>
                        </div>
                        <div class="modal-body">
                            <label for="product-search" class="form-label">Product</label>
                            <input type="text" class="form-control" id="product-search"
                                placeholder="Type product name or reference..."
                                wire:model.live.debounce.500ms="productSearch" autofocus>

                            @if (!empty($productResults))
                                <div class="mt-3">
                                    <ul class="list-group">
                                        @foreach ($productResults as $row)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="text-sm">
                                                    <div class="fw-bold">{{ $row->reference ?? $row->id }}</div>
                                                    <div class="text-muted">
                                                        {{ $row->getTranslation('name', 'en') ?? $row->name }}</div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-info mb-0"
                                                    wire:click="attachProduct({{ $row->id }})">
                                                    Add
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @elseif(strlen($productSearch) > 1)
                                <div class="text-sm text-muted mt-3">No results found.</div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto"
                                wire:click="closeProductsModal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            @include('inventory::livewire.items.partials.update-photo-modal')
            @include('inventory::livewire.items.partials.price-modal')
        </div>
    </div>
</div>
