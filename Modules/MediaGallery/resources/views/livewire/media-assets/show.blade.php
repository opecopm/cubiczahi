<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle"><a href="{{ route('admin.mediagallery.media-assets.index') }}">Media Gallery</a></div>
                    <h2 class="page-title">{{ $asset->displayTitle() }}</h2>
                </div>
                <div class="col-auto ms-auto d-flex gap-2">
                    <a href="{{ route('admin.mediagallery.media-assets.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                        Back
                    </a>
                    <a href="{{ $asset->previewUrl() }}" target="_blank" class="btn btn-outline-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                        Preview
                    </a>
                    <a href="{{ $asset->downloadUrl() }}" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                        Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">

                {{-- Preview --}}
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Preview</h3>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center bg-light rounded-bottom" style="min-height: 460px;">
                            @if($asset->isImage())
                                <img src="{{ $asset->previewUrl() }}" alt="{{ $asset->alt_text ?: $asset->displayTitle() }}" class="img-fluid">
                            @elseif($asset->isVideo())
                                <video controls class="w-100">
                                    <source src="{{ $asset->previewUrl() }}" type="{{ $asset->mime_type }}">
                                </video>
                            @elseif($asset->isAudio())
                                <audio controls class="w-100 px-3">
                                    <source src="{{ $asset->previewUrl() }}" type="{{ $asset->mime_type }}">
                                </audio>
                            @elseif($asset->isPdf())
                                <iframe src="{{ $asset->previewUrl() }}" title="{{ $asset->displayTitle() }}" style="width: 100%; min-height: 460px; border: 0;"></iframe>
                            @else
                                <div class="text-center px-4 py-5 text-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-3" width="72" height="72" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                    <h5>Inline preview not available</h5>
                                    <p class="text-secondary mb-3">This file type can still be opened in a new tab or downloaded.</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ $asset->previewUrl() }}" target="_blank" class="btn btn-sm btn-outline-info">Open</a>
                                        <a href="{{ $asset->downloadUrl() }}" class="btn btn-sm btn-outline-success">Download</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">

                    {{-- Asset Information --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Asset Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Name</div>
                                    <div class="datagrid-content">{{ $asset->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Title</div>
                                    <div class="datagrid-content">{{ $asset->title ?: '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Disk</div>
                                    <div class="datagrid-content">{{ $asset->disk ?: config('mediagallery.default_disk', 'public') }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Visibility</div>
                                    <div class="datagrid-content">{{ ucfirst($asset->visibility) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Type</div>
                                    <div class="datagrid-content">{{ ucfirst($asset->kind) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Mime Type</div>
                                    <div class="datagrid-content">{{ $asset->mime_type ?: '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Size</div>
                                    <div class="datagrid-content">{{ $asset->formattedSize() }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Folder</div>
                                    <div class="datagrid-content">{{ $asset->folder ?: '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Status</div>
                                    <div class="datagrid-content">
                                        <span class="badge {{ $asset->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Company</div>
                                    <div class="datagrid-content">{{ $asset->company?->name ?? '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Created</div>
                                    <div class="datagrid-content">{{ optional($asset->created_at)->format('Y-m-d H:i') ?: '—' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Links</div>
                                    <div class="datagrid-content">
                                        <span class="badge bg-blue-lt">{{ $asset->links_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description & Tags --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Description & Tags</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-secondary">{{ $asset->description ?: 'No description added yet.' }}</p>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                @forelse($asset->tagList() as $tag)
                                    <span class="badge bg-secondary-lt">{{ $tag }}</span>
                                @empty
                                    <span class="text-secondary small">No tags added.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Usage Links --}}
            <div class="row g-3 mt-0">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">Usage Links</h3>
                                <div class="card-subtitle">Where this media asset is attached across the ERP.</div>
                            </div>
                        </div>
                        @if($asset->links->isEmpty())
                            <div class="card-body">
                                <div class="alert alert-secondary mb-0">
                                    This asset is not linked to any module records yet.
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Model</th>
                                            <th>Record</th>
                                            <th>Usage</th>
                                            <th>Collection</th>
                                            <th>Primary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asset->links as $link)
                                            <tr>
                                                <td class="fw-bold">{{ $link->linkableTypeLabel() }}</td>
                                                <td>{{ $link->linkableDisplayName() }}</td>
                                                <td class="text-secondary">{{ $link->usage ?: '—' }}</td>
                                                <td class="text-secondary">{{ $link->collection_name ?: '—' }}</td>
                                                <td>
                                                    <span class="badge {{ $link->is_primary ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                        {{ $link->is_primary ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
