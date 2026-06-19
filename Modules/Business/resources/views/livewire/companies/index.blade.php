<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Company Management',
        'breadcrumbs' => [['label' => 'Companies', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Company',
            'wireClick' => 'openModal',
            'icon' => 'ti ti-plus',
            'class' => 'btn btn-primary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            {{-- Stats --}}
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.is_active', '')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-blue text-white avatar">{{ $companiesCount }}</span></div>
                                <div class="col"><div class="text-secondary">All Companies</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.is_active', 1)">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-green text-white avatar">{{ $activeCompaniesCount }}</span></div>
                                <div class="col"><div class="text-secondary">Active</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.is_active', 0)">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-secondary text-white avatar">{{ $inactiveCompaniesCount }}</span></div>
                                <div class="col"><div class="text-secondary">Inactive</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search...">
                        </div>
                        @foreach($model->filterable as $field => $meta)
                            <div class="col">
                                @if(($meta['type'] ?? null) === 'select')
                                    <select class="form-select" wire:model.live="filters.{{ $field }}">
                                        <option value="">{{ ucfirst(str_replace('_',' ',$field)) }}: All</option>
                                        @foreach(($meta['options'] ?? []) as $key => $val)
                                            <option value="{{ $key }}">{{ is_array($val) ? ($val['name'] ?? reset($val)) : $val }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($meta['type'] ?? null) === 'date')
                                    <input type="date" class="form-control" wire:model.live="filters.{{ $field }}">
                                @else
                                    <input type="text" class="form-control" wire:model.live="filters.{{ $field }}" placeholder="Search {{ ucfirst(str_replace('_',' ',$field)) }}">
                                @endif
                            </div>
                        @endforeach
                        <div class="col-auto">
                            <button wire:click="resetFilters" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th style="width:25%">Name (EN) @include('components.table.sort', ['field' => 'name'])</th>
                                @foreach($activeLanguages as $lang)
                                    @if($lang !== 'en')
                                        <th style="width:25%">Name ({{ strtoupper($lang) }})</th>
                                    @endif
                                @endforeach
                                <th>Code</th>
                                <th>CRN</th>
                                <th>TRN</th>
                                <th>Active</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                @include('business::livewire.companies.company-row', ['company' => $company, 'level' => 0, 'activeLanguages' => $activeLanguages])
                            @empty
                                <tr><td colspan="8" class="text-center text-secondary py-4">No companies found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($companies->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <small class="text-secondary">
                        Showing {{ $companies->firstItem() ?? 0 }} to {{ $companies->lastItem() ?? 0 }} of {{ $companies->total() }}
                    </small>
                    {{ $companies->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Company' : 'Add Company' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name (EN)</label>
                            <input type="text" class="form-control" wire:model.defer="name.en">
                            @error('name.en')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        @foreach($activeLanguages as $lang)
                            @if($lang !== 'en')
                                <div class="col-md-6">
                                    <label class="form-label">Name ({{ strtoupper($lang) }})</label>
                                    <input type="text" class="form-control" wire:model.defer="name.{{ $lang }}">
                                    @error("name.{$lang}")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            @endif
                        @endforeach
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" wire:model.defer="code">
                            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Parent Company</label>
                            @php
                                function renderCompanyOptions($companies, $prefix = '') {
                                    foreach ($companies as $company) {
                                        echo '<option value="' . $company->id . '">' . $prefix . $company->getTranslation('name', 'en') . '</option>';
                                        if ($company->children && $company->children->count()) {
                                            renderCompanyOptions($company->children, $prefix . '— ');
                                        }
                                    }
                                }
                            @endphp
                            <select class="form-select" wire:model="parent_id">
                                <option value="">Select Parent Company</option>
                                @php renderCompanyOptions($parent_companies); @endphp
                            </select>
                            @error('parent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">HR</label>
                            <select class="form-select" wire:model.defer="hr_id">
                                <option value="">Select HR</option>
                                @foreach($employees ?? [] as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) }}</option>
                                @endforeach
                            </select>
                            @error('hr_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">VP</label>
                            <select class="form-select" wire:model.defer="vp_id">
                                <option value="">Select VP</option>
                                @foreach($employees ?? [] as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) }}</option>
                                @endforeach
                            </select>
                            @error('vp_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CRN</label>
                            <input type="text" class="form-control" wire:model.defer="crn">
                            @error('crn')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">TRN</label>
                            <input type="text" class="form-control" wire:model.defer="trn">
                            @error('trn')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model.defer="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" wire:model.defer="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" wire:model.defer="website">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Invoice Code</label>
                            <input type="text" class="form-control" wire:model.defer="invoice_code">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" wire:model.defer="currency">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Company Logo</label>
                            <div class="border rounded p-2 mb-2" style="min-height:90px">
                                <img src="{{ $logoUpload ? $logoUpload->temporaryUrl() : $editingCompany->logo_url }}" style="height:80px;object-fit:contain">
                            </div>
                            <input type="file" class="form-control" wire:model="logoUpload" accept="image/*">
                            @error('logoUpload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Company Footer</label>
                            <div class="border rounded p-2 mb-2" style="min-height:90px">
                                <img src="{{ $footerUpload ? $footerUpload->temporaryUrl() : $editingCompany->footer_url }}" style="height:80px;object-fit:contain">
                            </div>
                            <input type="file" class="form-control" wire:model="footerUpload" accept="image/*">
                            @error('footerUpload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Company Stamp</label>
                            <div class="border rounded p-2 mb-2" style="min-height:90px">
                                <img src="{{ $stampUpload ? $stampUpload->temporaryUrl() : ($editingCompany->stamp_url ?: asset('assets/img/no-photo.jpg')) }}" style="height:80px;object-fit:contain">
                            </div>
                            <input type="file" class="form-control" wire:model="stampUpload" accept="image/*">
                            @error('stampUpload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_group" wire:model.defer="is_group">
                                <label class="form-check-label" for="is_group">Is Group</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" wire:model.defer="is_active">
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Cancel</button>
                    @if ($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update">Update</button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store">Save</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal modal-blur fade @if($showDeleteModal ?? false) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showDeleteModal ?? false) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Are you sure?</div>
                    <div>Do you really want to delete this company?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
