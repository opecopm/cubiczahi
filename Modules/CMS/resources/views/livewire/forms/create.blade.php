<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ isset($formId) ? 'Edit Form' : 'Create New Form' }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button type="button" wire:click="save" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> {{ isset($formId) ? 'Update Form' : 'Save Form' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <form wire:submit.prevent="save">
                <div class="row g-3">

                    <!-- Left Column: Form Settings & Builder -->
                    <div class="col-lg-8">

                        <!-- General Settings -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">General Information</h3>
                            </div>
                            <div class="card-body">
                                @php($languages = ['en' => 'English'])
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($languages as $locale => $label)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if($loop->first) active @endif" id="tab-{{ $locale }}"
                                                data-bs-toggle="tab" data-bs-target="#content-{{ $locale }}" type="button"
                                                role="tab">
                                                {{ $label }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($languages as $locale => $label)
                                        <div class="tab-pane fade @if($loop->first) show active @endif"
                                            id="content-{{ $locale }}" role="tabpanel">

                                            <div class="mb-3">
                                                <label class="form-label">Form Title ({{ $label }})</label>
                                                <input type="text" wire:model="title.{{ $locale }}"
                                                    class="form-control" required>
                                                @error('title.' . $locale) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Description ({{ $label }})</label>
                                                <textarea wire:model="description.{{ $locale }}"
                                                    class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Form Builder -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Form Fields</h3>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-plus me-1"></i> Add Field
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('text')">Text Input</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('email')">Email</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('number')">Number</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('textarea')">Textarea</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('select')">Select Box</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('checkbox')">Checkbox</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('radio')">Radio Buttons</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('date')">Date Picker</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="addField('file')">File Upload</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body bg-light">
                                @if(empty($fields))
                                    <div class="text-center p-4 text-muted">No fields added yet. Click "Add Field" to start.</div>
                                @endif

                                @foreach($fields as $index => $field)
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#field-{{ $index }}" style="cursor: pointer;">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary me-2">{{ strtoupper($field['type']) }}</span>
                                                <span class="fw-bold">{{ $field['label']['en'] ?? 'New Field' }}</span>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-ghost-secondary" wire:click.prevent="moveFieldUp({{ $index }})">
                                                    <i class="ti ti-arrow-up"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-ghost-secondary" wire:click.prevent="moveFieldDown({{ $index }})">
                                                    <i class="ti ti-arrow-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-ghost-danger" wire:click.prevent="removeField({{ $index }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="field-{{ $index }}" class="collapse show p-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Label (EN)</label>
                                                    <input type="text" wire:model="fields.{{ $index }}.label.en" class="form-control mb-2">
                                                    <label class="form-label">Label (AR)</label>
                                                    <input type="text" wire:model="fields.{{ $index }}.label.ar" class="form-control text-end" dir="rtl">
                                                    @error('fields.'.$index.'.label.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Field Name (Unique Slug)</label>
                                                    <input type="text" wire:model="fields.{{ $index }}.name" class="form-control" placeholder="e.g. first_name">
                                                    @error('fields.'.$index.'.name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Width</label>
                                                    <select wire:model="fields.{{ $index }}.width" class="form-control">
                                                        <option value="12">Full Width (100%)</option>
                                                        <option value="6">Half Width (50%)</option>
                                                        <option value="4">One Third (33%)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3 d-flex align-items-center">
                                                    <div class="mt-4">
                                                        <label class="form-check">
                                                            <input class="form-check-input" type="checkbox" wire:model="fields.{{ $index }}.is_required" id="req-{{ $index }}">
                                                            <span class="form-check-label">Required Field</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <a class="text-primary small" data-bs-toggle="collapse" href="#extra-{{ $index }}">Show Advanced Options</a>
                                                    <div class="collapse mt-2" id="extra-{{ $index }}">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <label class="form-label">Placeholder (EN)</label>
                                                                <input type="text" wire:model="fields.{{ $index }}.placeholder.en" class="form-control mb-2">
                                                                <label class="form-label">Placeholder (AR)</label>
                                                                <input type="text" wire:model="fields.{{ $index }}.placeholder.ar" class="form-control text-end" dir="rtl">
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label class="form-label">Help Text (EN)</label>
                                                                <input type="text" wire:model="fields.{{ $index }}.help_text.en" class="form-control mb-2">
                                                                <label class="form-label">Help Text (AR)</label>
                                                                <input type="text" wire:model="fields.{{ $index }}.help_text.ar" class="form-control text-end" dir="rtl">
                                                            </div>

                                                            @if(in_array($field['type'], ['select', 'radio', 'checkbox']))
                                                                <div class="col-12 mt-2">
                                                                    <label class="form-label fw-bold">Options (For Select/Radio/Checkbox)</label>
                                                                    <textarea wire:model="fields.{{ $index }}.options" class="form-control" rows="3" placeholder="Enter options separated by comma (e.g. Red, Blue, Green) or JSON format"></textarea>
                                                                    <small class="text-muted">Simple comma values or JSON.</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings -->
                    <div class="col-lg-4">

                        <!-- Publish Status -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Publish</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select wire:model="status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Notifications</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Admin Email(s)</label>
                                    <input type="text" wire:model="notification_emails" class="form-control" placeholder="admin@example.com">
                                    <small class="text-muted">Comma separated for multiple emails.</small>
                                </div>

                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="auto_responder" id="autoCheck">
                                        <span class="form-check-label">Send Auto-Response to User</span>
                                    </label>
                                </div>

                                <div class="mt-3">
                                    <a data-bs-toggle="collapse" href="#mailerSettings" class="text-primary small">Configure Custom Mailer (SMTP)</a>
                                    <div class="collapse mt-2" id="mailerSettings" wire:ignore.self>
                                        <div class="p-3 bg-light rounded border">
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">Driver</label>
                                                <select wire:model="mail_settings.driver" class="form-control">
                                                    <option value="smtp">SMTP</option>
                                                    <option value="gmail">Gmail (via SMTP)</option>
                                                    <option value="mailpit">Mailpit (Local)</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">Host</label>
                                                <input type="text" wire:model="mail_settings.host" class="form-control">
                                            </div>
                                            <div class="row">
                                                <div class="col-6 mb-2">
                                                    <label class="form-label small text-muted">Port</label>
                                                    <input type="text" wire:model="mail_settings.port" class="form-control">
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label small text-muted">Encryption</label>
                                                    <input type="text" wire:model="mail_settings.encryption" class="form-control" placeholder="tls, ssl">
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">Username</label>
                                                <input type="text" wire:model="mail_settings.username" class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">Password</label>
                                                <input type="password" wire:model="mail_settings.password" class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">From Address</label>
                                                <input type="email" wire:model="mail_settings.from_address" class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">From Name</label>
                                                <input type="text" wire:model="mail_settings.from_name" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Form Buttons</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Submit Button</label>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small text-muted">Label (EN)</label>
                                            <input type="text" wire:model="button_settings.submit_text.en" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small text-muted">CSS Class</label>
                                            <input type="text" wire:model="button_settings.submit_class" class="form-control" placeholder="btn-primary">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-muted">Reset Button</label>
                                    <div class="mb-2">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="button_settings.use_reset" id="resetCheck">
                                            <span class="form-check-label">Enable Reset Button</span>
                                        </label>
                                    </div>
                                    <div class="row" x-show="$wire.button_settings.use_reset">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small text-muted">Label (EN)</label>
                                            <input type="text" wire:model="button_settings.reset_text.en" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small text-muted">CSS Class</label>
                                            <input type="text" wire:model="button_settings.reset_class" class="form-control" placeholder="btn-secondary">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Security</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="use_captcha" id="captchaCheck">
                                        <span class="form-check-label">Enable CAPTCHA</span>
                                    </label>
                                </div>
                                <div class="mb-2">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="use_honeypot" id="honeyCheck">
                                        <span class="form-check-label">Enable Honeypot (Anti-Spam)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
