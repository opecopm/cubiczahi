@extends('themes.laundry-one.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- ── Page Hero ─────────────────────────────────────────────────── --}}
<section class="page-hero" style="background: linear-gradient(135deg, #0a2463 0%, #05163d 100%); padding: 5rem 0 4rem; color: white;">
    <div class="container hero-container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @include(theme_view('partials.breadcrumb'), [
                    'variant' => 'light',
                    'items' => [
                        ['label' => $page->title],
                    ],
                ])
                @if($page->subtitle)
                    <div class="hero-badge mt-2" style="background: rgba(255,255,255,0.15); display: inline-block; padding: 0.4rem 1.2rem; border-radius: 50px; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">
                        {{ $page->subtitle }}
                    </div>
                @endif
                <h1 class="hero-title mt-3 mb-3" style="font-size: 3rem; font-weight: 800; line-height: 1.2;">
                    <span>{{ $page->title }}</span>
                </h1>
                @if($page->alternative_title)
                    <p class="hero-subtitle mx-auto opacity-75" style="max-width: 600px; font-size: 1.1rem;">
                        {{ $page->alternative_title }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ── Main Page Content & Sections ─────────────────────────────── --}}
@if($page->content)
    <section class="section-white py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="page-body-content text-muted" style="line-height: 1.8; font-size: 1.1rem;">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@foreach($page->sections as $index => $section)
    <section class="{{ $index % 2 === 0 ? 'section-white' : 'section-light' }}" style="padding: 5rem 0; {{ $section->background_color ? 'background-color: ' . $section->background_color . ' !important;' : '' }}">
        <div class="container">
            {{-- Section Headers --}}
            @if($section->title || $section->subtitle || $section->badge)
                <div class="text-center mb-5">
                    @if($section->badge)
                        <div class="section-label" style="text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; font-size: 0.85rem; color: #0a2463; margin-bottom: 0.75rem;">
                            {{ $section->badge }}
                        </div>
                    @endif
                    @if($section->title)
                        <h2 class="section-title fw-bold text-dark" style="font-size: 2.25rem;">{{ $section->title }}</h2>
                    @endif
                    @if($section->subtitle)
                        <p class="section-subtitle mt-2 text-muted mx-auto" style="max-width: 650px; font-size: 1.05rem;">{{ $section->subtitle }}</p>
                    @endif
                </div>
            @endif

            {{-- Section description/text --}}
            @if($section->description)
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-9 text-center text-muted" style="font-size: 1.05rem; line-height: 1.7;">
                        {!! $section->description !!}
                    </div>
                </div>
            @endif

            {{-- Grid of Page Blocks in this section --}}
            @if($section->blocks && $section->blocks->count() > 0)
                <div class="row g-4 justify-content-center align-items-stretch">
                    @foreach($section->blocks as $block)
                        <div class="col-md-{{ $block->column_width ?? '12' }} d-flex">
                            <div class="w-100 transition-all duration-300" style="background: none; border: none;">
                                @switch($block->type)
                                    @case('text')
                                        <div class="text-block h-100 p-2">
                                            @if($block->heading)
                                                <h4 class="fw-bold text-dark mb-3">{{ $block->heading }}</h4>
                                            @endif
                                            @if($block->subheading)
                                                <h6 class="text-primary fw-bold mb-3">{{ $block->subheading }}</h6>
                                            @endif
                                            <div class="text-muted" style="line-height: 1.7;">
                                                {!! $block->description !!}
                                            </div>
                                        </div>
                                        @break

                                    @case('heading')
                                        <div class="heading-block text-center py-2 w-100">
                                            @if($block->badge)
                                                <div class="section-label mb-2" style="color: #0a2463; text-transform: uppercase; font-weight: 700; font-size: 0.8rem;">{{ $block->badge }}</div>
                                            @endif
                                            <h2 class="section-title text-dark fw-bold mb-2">{{ $block->heading }}</h2>
                                            @if($block->subheading)
                                                <p class="section-subtitle text-muted mt-1">{{ $block->subheading }}</p>
                                            @endif
                                        </div>
                                        @break

                                    @case('stat')
                                        <div class="about-stat bg-white p-4 rounded-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center align-items-center w-100" style="border: 1px solid #f1f5f9;">
                                            <div class="about-stat__n text-primary fw-extrabold" style="font-size: 3rem; line-height: 1;">
                                                {{ $block->heading }}<span style="font-size: 2rem; vertical-align: middle;">{{ $block->items_list['suffix'] ?? '' }}</span>
                                            </div>
                                            <div class="about-stat__l text-muted mt-2 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
                                                {{ $block->subheading ?: strip_tags($block->description) }}
                                            </div>
                                        </div>
                                        @break

                                    @case('image')
                                        <div class="image-block text-center position-relative overflow-hidden rounded-4 shadow-sm w-100 h-100 d-flex align-items-center justify-content-center" style="min-height: 250px; background: #f8fafc;">
                                            @php
                                                $imgUrl = $block->getFirstMediaUrl('content_image') ?: $block->icon_image;
                                            @endphp
                                            @if($imgUrl)
                                                <img src="{{ $imgUrl }}" alt="{{ $block->heading ?? 'Image' }}" class="img-fluid w-100 h-100" style="object-fit: cover; border-radius: 16px;">
                                            @else
                                                <div class="text-muted p-5">&#128247; No Image</div>
                                            @endif
                                        </div>
                                        @break

                                    @case('feature')
                                        <div class="service-card bg-white p-4 rounded-4 shadow-sm text-center h-100 d-flex flex-column align-items-center w-100" style="border: 1px solid #f1f5f9;">
                                            <div class="service-icon svc-icon--blue mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #eff6ff; color: #3b82f6;">
                                                @if($block->icon_class)
                                                    <i class="{{ $block->icon_class }}"></i>
                                                @elseif($block->icon_image)
                                                    <img src="{{ $block->icon_image }}" alt="icon" style="width: 28px; height: 28px;">
                                                @else
                                                    &#10024;
                                                @endif
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">{{ $block->heading }}</h5>
                                            <p class="text-muted card-desc mb-0 small" style="line-height: 1.6;">{{ $block->subheading ?: strip_tags($block->description) }}</p>
                                        </div>
                                        @break

                                    @case('card')
                                        <div class="card border-0 shadow-sm p-4 rounded-4 h-100 bg-white d-flex flex-column justify-content-between w-100" style="border: 1px solid #f1f5f9;">
                                            <div>
                                                @if($block->badge)
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill mb-3" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">{{ $block->badge }}</span>
                                                @endif
                                                <h5 class="fw-bold text-dark mb-2">{{ $block->heading }}</h5>
                                                <p class="text-muted small" style="line-height: 1.6;">{{ $block->subheading ?: strip_tags($block->description) }}</p>
                                            </div>
                                            @if($block->btn_text)
                                                <a href="{{ $block->btn_link ?: '#' }}" class="btn btn-outline-primary btn-sm fw-bold rounded-pill mt-3 w-100 py-2">{{ $block->btn_text }}</a>
                                            @endif
                                        </div>
                                        @break

                                    @case('business_partners')
                                        <div class="w-100 py-3">
                                            @php
                                                $partners = $block->items_list ?? [];
                                                if (isset($partners['partners_display_mode'])) unset($partners['partners_display_mode']);
                                                if (isset($partners['partners_limit_count'])) unset($partners['partners_limit_count']);
                                                $partners = array_filter($partners);
                                            @endphp
                                            <div class="row g-3 justify-content-center align-items-center">
                                                @forelse($partners as $partner)
                                                    <div class="col-6 col-md-3 text-center">
                                                        <div class="p-3 bg-white rounded-3 shadow-sm" style="border: 1px solid #e2e8f0;">
                                                            <span class="fw-bold text-muted small">{{ $partner }}</span>
                                                        </div>
                                                    </div>
                                                @empty
                                                    @foreach(['Brand Alfa', 'Brand Beta', 'Brand Gamma', 'Brand Delta'] as $brand)
                                                        <div class="col-6 col-md-3 text-center">
                                                            <div class="p-3 bg-white rounded-3 shadow-sm" style="border: 1px solid #e2e8f0;">
                                                                <span class="fw-bold text-muted small">{{ $brand }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforelse
                                            </div>
                                        </div>
                                        @break

                                    @case('project')
                                        @php
                                            $projects = [];
                                            if (class_exists(\Modules\CMS\Models\Project::class)) {
                                                $projectQuery = \Modules\CMS\Models\Project::where('is_active', true);
                                                $displayMode = $block->items_list['project_display_mode'] ?? 'all';
                                                if ($displayMode === 'category' && !empty($block->items_list['project_category_id'])) {
                                                    $projectQuery->whereHas('categories', function($q) use ($block) {
                                                        $q->where('cms_project_categories.category_id', $block->items_list['project_category_id']);
                                                    });
                                                }
                                                $limit = $block->items_list['project_limit_count'] ?? 6;
                                                if ($limit) {
                                                    $projectQuery->take((int)$limit);
                                                }
                                                $projects = $projectQuery->get();
                                            }
                                        @endphp
                                        <div class="w-100">
                                            <div class="row g-4">
                                                @forelse($projects as $project)
                                                    <div class="col-md-4">
                                                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100" style="border: 1px solid #f1f5f9;">
                                                            @if($project->main_image)
                                                                <img src="{{ media_url($project->main_image) }}" alt="{{ $project->project_title }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                                                            @endif
                                                            <div class="card-body p-3">
                                                                <h6 class="fw-bold text-dark mb-2">{{ $project->project_title }}</h6>
                                                                <p class="text-muted small mb-0">{{ Str::limit($project->short_description ?: strip_tags($project->project_description), 80) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-center text-muted py-3">No dynamic projects available.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                        @break

                                    @case('testimonials')
                                        @php
                                            $testimonials = [];
                                            if (class_exists(\Modules\CMS\Models\Testimonial::class)) {
                                                $testimonialQuery = \Modules\CMS\Models\Testimonial::where('status', true)->orderBy('sort_order', 'asc');
                                                $limit = $block->items_list['testimonials_limit_count'] ?? 3;
                                                if ($limit) {
                                                    $testimonialQuery->take((int)$limit);
                                                }
                                                $testimonials = $testimonialQuery->get();
                                            }
                                        @endphp
                                        <div class="w-100">
                                            <div class="row g-4 justify-content-center">
                                                @forelse($testimonials as $testimonial)
                                                    <div class="col-md-4">
                                                        <div class="testimonial-card bg-white p-4 rounded-4 shadow-sm h-100 d-flex flex-column justify-content-between" style="border: 1px solid #f1f5f9;">
                                                            <div>
                                                                <div class="stars mb-2 text-warning">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                                                <p class="text-muted small fst-italic mb-3" style="line-height: 1.6;">"{{ $testimonial->quote }}"</p>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                                <div class="avatar-circle" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #3b82f6; color: white; font-weight: 700; font-size: 0.8rem;">
                                                                    {{ substr($testimonial->name, 0, 1) }}
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-dark small">{{ $testimonial->name }}</div>
                                                                </div>
                                                                @if($testimonial->designation)
                                                                    <div class="text-muted small ms-auto" style="font-size: 0.75rem;">{{ $testimonial->designation }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-center text-muted py-3">No customer reviews available yet.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                        @break

                                    @case('service_listing')
                                        @php
                                            $services = [];
                                            if (class_exists(\Modules\Inventory\Models\Item::class)) {
                                                $services = \Modules\Inventory\Models\Item::where('status', 'active')
                                                    ->where('type', 'service')
                                                    ->with(['primaryImage', 'prices'])
                                                    ->take($block->items_list['service_listing_limit_count'] ?? 3)
                                                    ->get();
                                            }
                                        @endphp
                                        <div class="w-100">
                                            <div class="row g-4">
                                                @forelse($services as $service)
                                                    @php
                                                        $sellPrice = $service->prices->where('price_type','sell')->first();
                                                        $currencySymbol = '$';
                                                    @endphp
                                                    <div class="col-md-4">
                                                        <div class="service-card bg-white p-4 rounded-4 shadow-sm h-100 d-flex flex-column justify-content-between" style="border: 1px solid #f1f5f9;">
                                                            <div>
                                                                <h6 class="fw-bold text-dark mb-2">{{ $service->name }}</h6>
                                                                <p class="text-muted small" style="line-height: 1.5;">{{ Str::limit(strip_tags($service->short_description ?? $service->description ?? ''), 80) }}</p>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                                                                @if($sellPrice)
                                                                    <span class="fw-bold text-primary small">{{ $currencySymbol }}{{ number_format($sellPrice->price, 2) }}</span>
                                                                @endif
                                                                <a href="{{ route('catalog.show', $service->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.75rem;" wire:navigate>Learn More</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-center text-muted py-3">No active services listed.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                        @break

                                    @case('blog_listing')
                                        @php
                                            $blogs = [];
                                            if (class_exists(\Modules\CMS\Models\Blog::class)) {
                                                $blogQuery = \Modules\CMS\Models\Blog::where('status', 'published')->orderBy('published_at', 'desc');
                                                if (!empty($block->items_list['blog_listing_category_id'])) {
                                                    $blogQuery->whereHas('categories', function($q) use ($block) {
                                                        $q->where('cms_blog_categories.id', $block->items_list['blog_listing_category_id']);
                                                    });
                                                }
                                                $limit = $block->items_list['blog_listing_limit_count'] ?? 3;
                                                if ($limit) {
                                                    $blogQuery->take((int)$limit);
                                                }
                                                $blogs = $blogQuery->get();
                                            }
                                        @endphp
                                        <div class="w-100">
                                            <div class="row g-4">
                                                @forelse($blogs as $blog)
                                                    <div class="col-md-4">
                                                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100" style="border: 1px solid #f1f5f9;">
                                                            @if($blog->featured_image)
                                                                <img src="{{ media_url($blog->featured_image) }}" alt="{{ $blog->title }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                                                            @endif
                                                            <div class="card-body p-3">
                                                                <div class="text-muted small mb-1" style="font-size: 0.75rem;">{{ $blog->published_at ? $blog->published_at->format('M d, Y') : '' }}</div>
                                                                <h6 class="fw-bold text-dark mb-2">{{ $blog->title }}</h6>
                                                                <p class="text-muted small mb-0">{{ Str::limit($blog->excerpt ?: strip_tags($blog->content), 80) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-center text-muted py-3">No dynamic blog posts found.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                        @break

                                    @case('pricing')
                                        <div class="pricing-card bg-white p-4 rounded-4 shadow-sm text-center h-100 d-flex flex-column justify-content-between w-100" style="border: 1px solid #f1f5f9;">
                                            <div>
                                                @if($block->badge)
                                                    <div class="text-center mb-3">
                                                        <span class="badge bg-primary rounded-pill px-3 py-1 text-white" style="font-size: 0.75rem;">{!! $block->badge !!}</span>
                                                    </div>
                                                @endif
                                                <div class="fw-bold text-dark mb-1 plan-name" style="font-size: 1.25rem;">{{ $block->heading }}</div>
                                                <p class="text-muted small plan-desc mb-3">{{ $block->subheading }}</p>
                                                <div class="price mb-4" style="font-size: 2.2rem; color: #0a2463; font-weight: 800;">
                                                    <span style="font-size: 1.2rem; vertical-align: super; font-weight: 600;">{{ $block->items_list['currency'] ?? '$' }}</span>{{ $block->items_list['price'] ?? '0' }}<span style="font-size: 0.9rem; font-weight: 500; color: #64748b;">{{ $block->items_list['period'] ?? '' }}</span>
                                                </div>
                                                <div class="mb-3 text-start">
                                                    @php
                                                        $features = $block->items_list['features'] ?? [];
                                                    @endphp
                                                    @foreach($features as $feat)
                                                        <div class="feature-item d-flex align-items-center mb-2" style="font-size: 0.85rem;">
                                                            <span class="check text-success me-2 fw-bold" style="font-size: 1rem;">&#10003;</span>
                                                            <span class="text-muted">{{ $feat }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @if($block->btn_text)
                                                <a href="{{ $block->btn_link ?: '#' }}" class="btn btn-primary w-100 fw-bold rounded-pill py-2 mt-3 btn-plan">{{ $block->btn_text }}</a>
                                            @endif
                                        </div>
                                        @break

                                    @default
                                        <div class="alert alert-warning small w-100">Dynamic block type not supported: {{ $block->type }}</div>
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Section buttons or custom items list if defined inside the section directly --}}
            @if($section->buttons && count($section->buttons) > 0)
                <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                    @foreach($section->buttons as $btn)
                        <a href="{{ $btn['link'] ?? '#' }}" class="btn btn-{{ $btn['style'] ?? 'primary' }} rounded-pill px-4 py-2 fw-semibold">{{ $btn['text'] ?? 'Click' }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endforeach

@include(theme_view('partials.footer'))

@endsection
