<div class="container-fluid py-4">
    <x-page.page-header
        title="Edit Account"
        :breadcrumbs="[
            ['label' => 'Accounts', 'url' => route('accounting.accounts.index')],
            ['label' => $code, 'url' => route('accounting.accounts.show', ['account' => $accountId])],
            ['label' => 'Edit', 'url' => null],
        ]"
        :actions="[
            [
                'label' => 'Back',
                'icon' => 'arrow_back',
                'url' => route('accounting.accounts.show', ['account' => $accountId]),
                'class' => 'bg-gradient-light'
            ]
        ]"
    />

    <div class="row content">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form wire:submit.prevent="update">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="code">Code</label>
                                    <input type="text" id="code" class="form-control" wire:model="code">
                                </div>
                                @error('code')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" class="form-control" wire:model="name">
                                </div>
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="type">Type</label>
                                    <select id="type" class="form-control" wire:model="type">
                                        @foreach ($types as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('type')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="currency">Currency</label>
                                    <input type="text" id="currency" class="form-control" wire:model="currency">
                                </div>
                                @error('currency')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="parent_id">Parent Account</label>
                                    <select id="parent_id" class="form-control" wire:model="parent_id">
                                        <option value="">None</option>
                                        @foreach ($parentAccounts as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('parent_id')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="active" wire:model="active">
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                                @error('active')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="description">Description</label>
                                    <textarea id="description" class="form-control" rows="3" wire:model="description"></textarea>
                                </div>
                                @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('accounting.accounts.show', ['account' => $accountId]) }}" class="btn btn-secondary mb-0">Cancel</a>
                            <button type="submit" class="btn btn-primary mb-0" wire:loading.attr="disabled">
                                <span wire:loading wire:target="update" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span wire:loading.remove wire:target="update">Save</span>
                                <span wire:loading wire:target="update">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

