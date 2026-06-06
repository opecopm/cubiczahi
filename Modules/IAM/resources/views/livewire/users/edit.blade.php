<div>
    @php
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    @endphp

    @component('admin.partials.page.inner-header', [
        'title' => 'Edit User: ' . $fullName,
        'breadcrumbs' => [
            [
                'label' => 'Users',
                'url' => route('admin.iam.users.index'),
                'icon' => 'back',
            ],
            [
                'label' => $fullName,
                'url' => route('admin.iam.users.show', $user->id),
                'class' => 'text-body fw-medium',
            ],
            [
                'label' => 'Edit',
                'active' => true,
            ],
        ],
        'actionItems' => [[
            'title' => 'Back',
            'route' => 'admin.iam.users.show',
            'params' => $user->id,
            'icon' => 'ti ti-arrow-left',
            'class' => 'btn btn-outline-secondary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible mb-3">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <span class="avatar avatar-xl" style="background-image: url({{ $user->getFirstMediaUrl('avatars') ?: asset('assets/img/no-photo.jpg') }})"></span>
                                </div>
                                <h4 class="mb-0">{{ $fullName }}</h4>
                                <p class="text-secondary mb-3">{{ $user->email }}</p>
                                <div class="text-start">
                                    <label class="form-label">Avatar</label>
                                    <input type="file" class="form-control" wire:model="avatar">
                                    @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">User Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" wire:model="first_name">
                                        @error('first_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" wire:model="last_name">
                                        @error('last_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" wire:model="email">
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" wire:model="password" placeholder="Leave blank to keep current">
                                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" wire:model="role">
                                            <option value="">Please Select</option>
                                            @foreach ($roles as $r)
                                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Menu</label>
                                        <select class="form-select" wire:model="menu_id">
                                            <option value="">Please Select</option>
                                            @foreach ($menus as $m)
                                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('menu_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" wire:model="status">
                                            <option value="">Select Status</option>
                                            @foreach (App\Models\User::STATUS_SELECT as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Primary Location</label>
                                        <select class="form-select" wire:model="location_id">
                                            <option value="">Not Set</option>
                                            @foreach ($locations as $loc)
                                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                        @error('location_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <a href="{{ route('admin.iam.users.show', $user->id) }}" class="btn btn-link link-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="avatar, save">
                                    <span wire:loading wire:target="avatar, save" class="spinner-border spinner-border-sm me-2"></span>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
