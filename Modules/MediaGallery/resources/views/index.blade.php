@extends('admin.layouts.app')
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Modules</div>
                <h2 class="page-title">Media Gallery</h2>
                <div class="page-subtitle">Central library for reusable files backed by the shared <code>media</code> table and polymorphic links.</div>
            </div>
            <div class="col-auto ms-auto d-flex gap-2">
                <span class="badge bg-info-lt">{{ config('mediagallery.collection_name', 'original') }}</span>
                <span class="badge bg-secondary-lt">{{ config('mediagallery.default_disk', 'public') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ number_format($stats['total_assets']) }}</div>
                                <div class="text-secondary">Total Assets</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ number_format($stats['active_assets']) }}</div>
                                <div class="text-secondary">Active Assets</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-cyan text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ number_format($stats['image_assets']) }}</div>
                                <div class="text-secondary">Images</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-orange text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6"/><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ number_format($stats['linked_assets']) }}</div>
                                <div class="text-secondary">Linked Assets</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Latest Assets</h3>
                <div class="card-subtitle">Recent reusable media records from <code>media_assets</code>.</div>
            </div>
            <div class="card-body">
                @if($latestAssets->isEmpty())
                    <div class="alert alert-info">
                        The module is ready. Run migrations, then start creating gallery assets and link them to HRM, CRM, Inventory, or DMS records.
                    </div>
                @else
                    <div class="row row-cards">
                        @foreach($latestAssets as $asset)
                            @php
                                $isImage = str_starts_with((string) $asset->mime_type, 'image/');
                                $previewUrl = $asset->primaryUrl();
                            @endphp
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="card card-sm h-100">
                                    <div class="card-body">
                                        <div class="rounded overflow-hidden mb-3 bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                            @if($isImage && $previewUrl)
                                                <img src="{{ $previewUrl }}" alt="{{ $asset->alt_text ?: $asset->title ?: $asset->name }}" class="img-fluid h-100 w-100 object-fit-cover">
                                            @else
                                                <div class="text-center text-secondary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                                    <div class="small mt-1">{{ strtoupper($asset->extension ?: $asset->kind) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="fw-bold">{{ $asset->title ?: $asset->name }}</div>
                                        <div class="text-secondary small">{{ $asset->disk ?: config('mediagallery.default_disk', 'public') }} · {{ $asset->visibility }}</div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-secondary small">{{ ucfirst($asset->kind) }}</span>
                                            <span class="badge bg-secondary-lt">{{ $asset->links_count }} links</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
