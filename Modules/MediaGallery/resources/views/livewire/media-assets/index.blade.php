<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Media Gallery</div>
                    <h2 class="page-title">Assets</h2>
                </div>
                <div class="col-auto ms-auto d-flex gap-2">
                    <button wire:click="openFolderCreateModal" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/><path d="M12 10l0 6"/><path d="M9 13l6 0"/></svg>
                        New Folder
                    </button>
                    <button wire:click="openDocumentCreateModal" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/></svg>
                        Upload Document
                    </button>
                    <button wire:click="openImageCreateModal" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/></svg>
                        Upload Image
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            {{-- Stats Row --}}
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
                                    <div class="font-weight-medium">{{ number_format($stats['total']) }}</div>
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
                                    <div class="font-weight-medium">{{ number_format($stats['active']) }}</div>
                                    <div class="text-secondary">Active</div>
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
                                    <div class="font-weight-medium">{{ number_format($stats['images']) }}</div>
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
                                    <div class="font-weight-medium">{{ number_format($stats['linked']) }}</div>
                                    <div class="text-secondary">Linked</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                {{-- Folder Tree --}}
                <div class="col-lg-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Folders</h3>
                            <div class="card-subtitle">Hierarchy view</div>
                            <div class="card-options">
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openFolderCreateModal">New</button>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="list-group list-group-flush">
                                <button type="button"
                                    class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ !$currentFolderId ? 'active' : '' }}"
                                    wire:click="goToRoot">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/></svg>
                                    Root
                                </button>

                                @if(empty($folderTreeItems))
                                    <div class="list-group-item text-secondary small">No folders created yet.</div>
                                @else
                                    @foreach($folderTreeItems as $folderItem)
                                        <div class="list-group-item border-0 p-0 {{ $folderItem['is_current'] ? 'bg-primary-lt' : '' }}"
                                             style="padding-left: {{ 0.5 + ($folderItem['depth'] * 1) }}rem !important;">
                                            <div class="d-flex align-items-center">
                                                <button type="button"
                                                    class="btn btn-link text-body text-decoration-none px-2 py-1 flex-grow-1 text-start"
                                                    wire:click="openFolder({{ $folderItem['id'] }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-yellow me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/></svg>
                                                    <span class="{{ $folderItem['is_current'] ? 'fw-bold' : '' }}">{{ $folderItem['name'] }}</span>
                                                </button>
                                                <div class="d-flex gap-1 pe-2">
                                                    <button type="button"
                                                        class="btn btn-sm btn-ghost-secondary px-1"
                                                        title="Create subfolder"
                                                        wire:click.stop="openChildFolderCreateModal({{ $folderItem['id'] }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-ghost-primary px-1"
                                                        title="Upload image"
                                                        wire:click.stop="uploadImageToFolder({{ $folderItem['id'] }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/></svg>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-ghost-secondary px-1"
                                                        title="Upload document"
                                                        wire:click.stop="uploadDocumentToFolder({{ $folderItem['id'] }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="col-lg-9">

                    {{-- Filters --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-2 align-items-end">
                                <div class="col-lg-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="File name, title, mime type…">
                                </div>
                                <div class="col-lg-2 col-md-3">
                                    <label class="form-label">Disk</label>
                                    <select class="form-select" wire:model.live="filters.disk">
                                        <option value="">All disks</option>
                                        @foreach($allowedDisks as $allowedDisk)
                                            <option value="{{ $allowedDisk }}">{{ strtoupper($allowedDisk) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-select" wire:model.live="filters.kind">
                                        <option value="">All types</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="audio">Audio</option>
                                        <option value="document">Document</option>
                                        <option value="file">File</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3">
                                    <label class="form-label">Visibility</label>
                                    <select class="form-select" wire:model.live="filters.visibility">
                                        <option value="">All</option>
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" wire:model.live="filters.status">
                                        <option value="">All</option>
                                        <option value="active">Active</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3">
                                    <label class="form-label">Per Page</label>
                                    <select class="form-select" wire:model.live="perPage">
                                        <option value="12">12</option>
                                        <option value="24">24</option>
                                        <option value="48">48</option>
                                        <option value="96">96</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3">
                                    <button type="button" class="btn btn-outline-secondary w-100" wire:click="resetFilters">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Drive Listing --}}
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">Drive Listing</h3>
                                <div class="card-subtitle">Folders appear first, then files inside the current location.</div>
                            </div>
                            <div class="card-options d-flex gap-2 align-items-center">
                                @if($currentFolder)
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="goToParentFolder">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M18 11l-6 -6"/><path d="M6 11l6 -6"/></svg>
                                        Up One Level
                                    </button>
                                @endif
                                {{ $assets->links() }}
                            </div>
                        </div>

                        {{-- Path breadcrumb --}}
                        <div class="card-body border-bottom py-2">
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <button type="button"
                                    class="btn btn-sm {{ !$currentFolder ? 'btn-primary' : 'btn-outline-secondary' }}"
                                    wire:click="goToRoot">Root</button>
                                @foreach($breadcrumbs as $breadcrumb)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-secondary" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6"/></svg>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openFolder({{ $breadcrumb->id }})">
                                        {{ $breadcrumb->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @if($folders->isEmpty() && $assets->isEmpty())
                            <div class="card-body">
                                <div class="alert alert-info mb-0">
                                    This location is empty. Create a folder or upload a file into the current location.
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Location</th>
                                            <th>Size / Count</th>
                                            <th>Updated</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($folders as $folder)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                        <button type="button" class="btn btn-link text-body text-decoration-none p-0 d-flex align-items-center gap-2" wire:click="openFolder({{ $folder->id }})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/></svg>
                                                            <span class="fw-bold">{{ $folder->name }}</span>
                                                        </button>
                                                        <div class="d-flex gap-1">
                                                            <button type="button" class="btn btn-sm btn-ghost-primary px-1"
                                                                title="Upload image to {{ $folder->name }}"
                                                                wire:click.stop="uploadImageToFolder({{ $folder->id }})">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/></svg>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-ghost-secondary px-1"
                                                                title="Upload document to {{ $folder->name }}"
                                                                wire:click.stop="uploadDocumentToFolder({{ $folder->id }})">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-secondary">Folder</td>
                                                <td class="text-secondary">{{ $folder->displayPath() }}</td>
                                                <td class="text-secondary">{{ $folder->children_count }} subfolders / {{ $folder->assets_count }} files</td>
                                                <td class="text-secondary">{{ optional($folder->updated_at)->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openFolder({{ $folder->id }})">Open</button>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @foreach($assets as $asset)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($asset->isImage())
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-cyan" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/></svg>
                                                        @elseif($asset->isVideo())
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l4.553 -2.069a1 1 0 0 1 1.447 .894v6.35a1 1 0 0 1 -1.447 .894l-4.553 -2.069v-4z"/><path d="M3 6a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-12z"/></svg>
                                                        @elseif($asset->isAudio())
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/><path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/><path d="M9 17v-13l10 -2v13"/><path d="M9 8l10 -2"/></svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-secondary" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $asset->displayTitle() }}</div>
                                                            <div class="text-secondary small">{{ $asset->disk }} · {{ $asset->visibility }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-secondary-lt">{{ ucfirst($asset->kind) }}</span></td>
                                                <td class="text-secondary">{{ $asset->folderPath() }}</td>
                                                <td class="text-secondary">{{ $asset->formattedSize() }}</td>
                                                <td class="text-secondary">{{ optional($asset->updated_at)->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ $asset->showUrl() }}" class="btn btn-sm btn-outline-secondary">Details</a>
                                                        <a href="{{ $asset->downloadUrl() }}" class="btn btn-sm btn-outline-success">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($assets->hasPages())
                                <div class="card-footer d-flex align-items-center">
                                    {{ $assets->links() }}
                                </div>
                            @endif
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Image Upload Modal --}}
    <div class="modal modal-blur fade @if($showImageModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showImageModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Image Asset' : 'Upload Image Asset' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeImageModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div wire:ignore wire:key="media-gallery-image-cropper">
                                <label class="form-label">Image File</label>
                                <input type="file" id="media-gallery-image-source" class="form-control" wire:model.live="mediaFile" accept="image/png,image/jpeg,image/webp">

                                <div class="row mt-3 d-none" id="media-gallery-cropper-workspace">
                                    <div class="col-lg-8 mb-3 mb-lg-0">
                                        <div class="bg-light border rounded p-3 text-center media-gallery-cropper-stage">
                                            <img id="media-gallery-cropper-image" class="img-fluid d-none" alt="Crop preview">
                                            <div id="media-gallery-cropper-placeholder" class="text-secondary py-5">
                                                Select an image to start cropping.
                                            </div>
                                        </div>
                                        <div class="text-secondary small mt-2">
                                            Drag to reposition, use the handles to resize, and pick an aspect ratio before applying the crop.
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">Aspect Ratio</label>
                                        <select id="media-gallery-crop-aspect" class="form-select mb-3">
                                            <option value="NaN">Free</option>
                                            <option value="1">Square 1:1</option>
                                            <option value="1.7777777778">Landscape 16:9</option>
                                            <option value="1.3333333333">Standard 4:3</option>
                                        </select>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="media-gallery-apply-crop">Apply Crop</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="media-gallery-crop-reset">Reset</button>
                                        </div>
                                        <div class="text-info small mt-3 d-none" id="media-gallery-crop-status">
                                            Crop prepared. You can now save the image asset.
                                        </div>
                                        <div class="text-success small mt-2 d-none" id="media-gallery-crop-file-wrapper">
                                            Prepared file: <span id="media-gallery-crop-file-name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-info small mt-2" wire:loading wire:target="mediaFile">Uploading image…</div>
                            @error('mediaFile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            @if($mediaFile)
                                <div class="text-success small mt-2">
                                    Ready: {{ $mediaFile->getClientOriginalName() }}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" wire:model.defer="title">
                            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Save To Folder</label>
                            <input type="text" class="form-control" value="{{ $currentFolder?->displayPath() ?? 'Root' }}" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Disk</label>
                            <select class="form-select" wire:model.defer="disk">
                                @foreach($allowedDisks as $allowedDisk)
                                    <option value="{{ $allowedDisk }}">{{ strtoupper($allowedDisk) }}</option>
                                @endforeach
                            </select>
                            @error('disk')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Visibility</label>
                            <select class="form-select" wire:model.defer="visibility">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                            </select>
                            @error('visibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#media-gallery-image-optional" aria-expanded="false">
                                Optional Fields
                            </button>
                        </div>

                        <div class="col-md-12">
                            <div class="collapse" id="media-gallery-image-optional">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Alt Text</label>
                                        <input type="text" class="form-control" wire:model.defer="alt_text">
                                        @error('alt_text')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tags</label>
                                        <input type="text" class="form-control" wire:model.defer="tags" placeholder="brand, logo, 2026">
                                        @error('tags')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" wire:model.defer="description" rows="3"></textarea>
                                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeImageModal">Cancel</button>
                    @if($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled" wire:target="mediaFile,update">
                            <span wire:loading wire:target="mediaFile,update" class="spinner-border spinner-border-sm me-2"></span>
                            Update Asset
                        </button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store" wire:loading.attr="disabled" wire:target="mediaFile,store">
                            <span wire:loading wire:target="mediaFile,store" class="spinner-border spinner-border-sm me-2"></span>
                            Save Image Asset
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Document Upload Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Document Asset' : 'Upload Document Asset' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeDocumentModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Document File</label>
                            <input type="file" class="form-control" wire:model.live="mediaFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv">
                            <div class="text-info small mt-2" wire:loading wire:target="mediaFile">Uploading document…</div>
                            @error('mediaFile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            @if($mediaFile)
                                <div class="text-secondary small mt-2">Selected file: {{ $mediaFile->getClientOriginalName() }}</div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" wire:model.defer="title">
                            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Save To Folder</label>
                            <input type="text" class="form-control" value="{{ $currentFolder?->displayPath() ?? 'Root' }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Disk</label>
                            <select class="form-select" wire:model.defer="disk">
                                @foreach($allowedDisks as $allowedDisk)
                                    <option value="{{ $allowedDisk }}">{{ strtoupper($allowedDisk) }}</option>
                                @endforeach
                            </select>
                            @error('disk')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Visibility</label>
                            <select class="form-select" wire:model.defer="visibility">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                            </select>
                            @error('visibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#media-gallery-document-optional" aria-expanded="false">
                                Optional Fields
                            </button>
                        </div>

                        <div class="col-md-12">
                            <div class="collapse" id="media-gallery-document-optional">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Tags</label>
                                        <input type="text" class="form-control" wire:model.defer="tags" placeholder="invoice, signed, 2026">
                                        @error('tags')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" wire:model.defer="description" rows="3"></textarea>
                                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeDocumentModal">Cancel</button>
                    @if($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled" wire:target="mediaFile,update">
                            <span wire:loading wire:target="mediaFile,update" class="spinner-border spinner-border-sm me-2"></span>
                            Update Asset
                        </button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store" wire:loading.attr="disabled" wire:target="mediaFile,store">
                            <span wire:loading wire:target="mediaFile,store" class="spinner-border spinner-border-sm me-2"></span>
                            Save Document Asset
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Create Folder Modal --}}
    <div class="modal modal-blur fade @if($showFolderModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showFolderModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Folder</h5>
                    <button type="button" class="btn-close" wire:click="closeFolderModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Parent Folder</label>
                        <input type="text" class="form-control" value="{{ $createFolderParent?->displayPath() ?? 'Root' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Folder Name</label>
                        <input type="text" class="form-control" wire:model.defer="folderName" placeholder="Marketing">
                        @error('folderName')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" wire:model.defer="folderDescription" rows="3"></textarea>
                        @error('folderDescription')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeFolderModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="storeFolder">Create Folder</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.livewire.partials.delete-confirmation-modal')

    @push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <style>
        .media-gallery-cropper-stage { min-height: 420px; }
        #media-gallery-cropper-image { max-height: 380px; max-width: 100%; }
    </style>
    @endpush

    @push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        (function () {
            const state = {
                cropper: null, workspace: null, imageElement: null, aspectSelect: null,
                statusElement: null, sourceInput: null,
                fileNameText: null, fileNameWrapper: null, placeholder: null, initialized: false,
            };

            function bindElements() {
                state.workspace      = document.getElementById('media-gallery-cropper-workspace');
                state.imageElement   = document.getElementById('media-gallery-cropper-image');
                state.aspectSelect   = document.getElementById('media-gallery-crop-aspect');
                state.statusElement  = document.getElementById('media-gallery-crop-status');
                state.sourceInput    = document.getElementById('media-gallery-image-source');
                state.fileNameText   = document.getElementById('media-gallery-crop-file-name');
                state.fileNameWrapper= document.getElementById('media-gallery-crop-file-wrapper');
                state.placeholder    = document.getElementById('media-gallery-cropper-placeholder');
            }

            function getLivewireComponent() {
                const root = state.sourceInput && state.sourceInput.closest('[wire\\:id]');
                return root ? window.Livewire?.find(root.getAttribute('wire:id')) : null;
            }

            function syncCroppedFile() {
                if (!state.cropper) return;
                const canvas = state.cropper.getCroppedCanvas({ maxWidth: 2000, maxHeight: 2000, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });
                if (!canvas) return;
                canvas.toBlob((blob) => {
                    if (!blob) return;
                    const fileName = 'cropped-' + Date.now() + '.png';
                    const file = new File([blob], fileName, { type: 'image/png' });
                    const component = getLivewireComponent();
                    if (!component) return;
                    component.upload(
                        'mediaFile',
                        file,
                        () => {
                            if (state.fileNameText) state.fileNameText.textContent = fileName;
                            if (state.fileNameWrapper) state.fileNameWrapper.classList.remove('d-none');
                            if (state.statusElement) state.statusElement.classList.remove('d-none');
                        },
                        () => {},
                    );
                }, 'image/png', 0.95);
            }

            function destroyCropper() {
                if (state.cropper) { state.cropper.destroy(); state.cropper = null; }
            }

            function resetCropper(clearInputs = true) {
                destroyCropper();
                if (state.workspace) state.workspace.classList.add('d-none');
                if (state.imageElement) { state.imageElement.src = ''; state.imageElement.classList.add('d-none'); }
                if (state.fileNameWrapper) state.fileNameWrapper.classList.add('d-none');
                if (state.fileNameText) state.fileNameText.textContent = '';
                if (state.statusElement) state.statusElement.classList.add('d-none');
                if (state.aspectSelect) state.aspectSelect.value = 'NaN';
                if (state.placeholder) state.placeholder.classList.remove('d-none');
                if (clearInputs && state.sourceInput) state.sourceInput.value = '';
            }

            function loadImage(file) {
                if (!file) { resetCropper(); return; }
                const reader = new FileReader();
                reader.onload = (event) => {
                    destroyCropper();
                    if (!state.imageElement) bindElements();
                    if (!state.imageElement) return;
                    state.imageElement.onload = () => {
                        if (state.workspace) state.workspace.classList.remove('d-none');
                        if (state.placeholder) state.placeholder.classList.add('d-none');
                        state.imageElement.classList.remove('d-none');
                        state.cropper = new Cropper(state.imageElement, {
                            viewMode: 1, dragMode: 'move', autoCropArea: 1,
                            responsive: true, background: false, aspectRatio: NaN,
                        });
                    };
                    state.imageElement.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }

            function attachListeners() {
                if (state.initialized) return;
                document.addEventListener('change', (event) => {
                    if (event.target && event.target.id === 'media-gallery-image-source') {
                        bindElements();
                        loadImage(event.target.files[0] || null);
                    }
                    if (event.target && event.target.id === 'media-gallery-crop-aspect' && state.cropper) {
                        const value = event.target.value;
                        state.cropper.setAspectRatio(value === 'NaN' ? NaN : parseFloat(value));
                    }
                });
                document.addEventListener('click', (event) => {
                    if (event.target && event.target.id === 'media-gallery-apply-crop') {
                        event.preventDefault();
                        syncCroppedFile();
                    }
                    if (event.target && event.target.id === 'media-gallery-crop-reset') {
                        event.preventDefault();
                        if (state.cropper) {
                            state.cropper.reset();
                            if (state.aspectSelect) {
                                const value = state.aspectSelect.value;
                                state.cropper.setAspectRatio(value === 'NaN' ? NaN : parseFloat(value));
                            }
                        }
                    }
                });
                window.addEventListener('media-gallery-reset-cropper', () => { bindElements(); resetCropper(); });
                window.addEventListener('media-gallery-image-modal-opened', () => { bindElements(); resetCropper(false); });
                state.initialized = true;
            }

            document.addEventListener('livewire:initialized', () => { bindElements(); attachListeners(); });
        })();
    </script>
    @endpush
</div>
