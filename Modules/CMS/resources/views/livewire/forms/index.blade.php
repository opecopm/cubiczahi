<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Form Management</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.cms.forms.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Form
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="flex-grow-1 me-3">
                            <form wire:submit.prevent="$refresh">
                                <input type="text" class="form-control" wire:model.live="search"
                                    placeholder="Search forms...">
                            </form>
                        </div>
                        <div class="flex-shrink-0">{{ $forms->links() }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    @php($currentLocale = app()->getLocale())
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Title ({{ strtoupper($currentLocale) }})</th>
                                <th>Status</th>
                                <th>Submissions</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($forms as $form)
                                <tr>
                                    <td>{{ $form->id }}</td>
                                    <td>{{ $form->getTranslation('title', $currentLocale) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $form->status === 'active' ? 'success' : 'secondary' }}-lt">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                        <div class="mt-1">
                                            @if($form->use_captcha)
                                                <i class="ti ti-shield text-secondary" title="CAPTCHA Enabled"></i>
                                            @endif
                                            @if($form->use_honeypot)
                                                <i class="ti ti-bug text-warning" title="Honeypot Enabled"></i>
                                            @endif
                                            @if(!empty($form->mail_settings['host']))
                                                <i class="ti ti-mail text-info" title="Custom Mailer"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $form->submissions->count() }}</td>
                                    <td>{{ $form->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.cms.forms.edit', $form->id) }}" class="btn btn-sm btn-icon btn-ghost-primary">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $form->id }})"
                                            class="btn btn-sm btn-icon btn-ghost-danger"
                                            wire:confirm="Are you sure you want to delete this form?">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $forms->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
