<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Customer Documents',
        'breadcrumbs' => [
            [
                'label' => 'Customers',
                'url' => route('admin.crm.customers.index'),
                'icon' => 'back',
            ],
            [
                'label' => $customer->name,
                'url' => route('admin.crm.customers.show', $customer->id),
                'class' => 'text-body fw-medium',
            ],
            [
                'label' => 'Documents',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'type' => 'button',
                'title' => 'Add Document',
                'wireClick' => 'create',
                'icon' => 'ti ti-plus',
                'class' => 'btn btn-primary',
            ],
        ],
    ])
        @slot('meta')
            {{ $customer->name }}
        @endslot
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Number</th>
                                <th>Issue Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                @php
                                    $fileUrl = $document->getFirstMediaUrl('documents');
                                @endphp
                                <tr>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ $document->type }}</td>
                                    <td>{{ $document->document_number ?: '—' }}</td>
                                    <td>{{ $document->issue_date ?: '—' }}</td>
                                    <td>{{ $document->expiry_date ?: '—' }}</td>
                                    <td>
                                        <span class="badge {{ $document->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if($fileUrl)
                                                    <a href="{{ $fileUrl }}" target="_blank" class="dropdown-item">View</a>
                                                @endif
                                                <button type="button" class="dropdown-item" wire:click="edit({{ $document->id }})">Edit</button>
                                                <button type="button" class="dropdown-item text-danger" wire:click="delete({{ $document->id }})" wire:confirm="Are you sure you want to delete this document?">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-4">No documents found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $documentId ? 'Edit Document' : 'Add Document' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="{{ $documentId ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <select class="form-select" wire:model="type">
                                    <option value="">Select Type</option>
                                    @foreach($documentTypes as $docType)
                                        <option value="{{ $docType->slug ?: $docType->name }}">{{ $docType->name }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Document Number</label>
                                <input type="text" class="form-control" wire:model="document_number">
                                @error('document_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Issuing Country</label>
                                <select class="form-select" wire:model="issuing_country">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('issuing_country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Issue Date</label>
                                <input type="date" class="form-control" wire:model="issue_date">
                                @error('issue_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" wire:model="expiry_date">
                                @error('expiry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Issuing Entity</label>
                                <input type="text" class="form-control" wire:model="issuing_entity">
                                @error('issuing_entity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" wire:model="description" rows="3"></textarea>
                                @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">File</label>
                                <input type="file" class="form-control" wire:model="file">
                                <div wire:loading wire:target="file" class="text-info mt-2">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    Uploading file...
                                </div>
                                @error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store,update">
                            <span wire:loading wire:target="store,update" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
