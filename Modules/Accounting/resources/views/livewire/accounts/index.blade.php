<div class="container-fluid py-4">
    <x-page.page-header
        title="Accounts"
        :breadcrumbs="[['label' => 'Accounts', 'url' => route('accounting.accounts.index')]]"
        :actions="[
            [
                'label' => 'Add Account',
                'icon' => 'add',
                'url' => route('accounting.accounts.create'),
                'class' => 'bg-gradient-dark'
            ]
        ]"
    />

    <div class="row content">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-body">
                    <x-page.counter-filter-cards :cards="[
                        [
                            'action' => '$set(\'filters.active\', \'\')',
                            'icon_bg_class' => 'bg-gradient-dark shadow-dark',
                            'icon' => 'account_balance',
                            'label' => 'All Accounts',
                            'count' => $accountsCount,
                            'footer_highlight' => 'All',
                            'footer_highlight_class' => 'text-success',
                            'footer_text' => 'accounts',
                        ],
                        [
                            'action' => '$set(\'filters.active\', \'1\')',
                            'icon_bg_class' => 'bg-gradient-success shadow-success',
                            'icon' => 'check_circle',
                            'label' => 'Active',
                            'count' => $activeAccountsCount,
                            'footer_highlight' => 'Active',
                            'footer_highlight_class' => 'text-success',
                            'footer_text' => 'accounts',
                        ],
                        [
                            'action' => '$set(\'filters.active\', \'0\')',
                            'icon_bg_class' => 'bg-gradient-secondary shadow-secondary',
                            'icon' => 'cancel',
                            'label' => 'Inactive',
                            'count' => $inactiveAccountsCount,
                            'footer_highlight' => 'Inactive',
                            'footer_highlight_class' => 'text-secondary',
                            'footer_text' => 'accounts',
                        ],
                    ]" />

                    <div class="row d-flex mt-3 g-2">
                        <div class="col">
                            <div class="input-group input-group-outline">
                                <label>Search</label>
                                <input type="text" class="form-control" wire:model.live="search" placeholder="Search by code or name">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group input-group-outline">
                                <label>Type</label>
                                <select class="form-control" wire:model.live="filters.type">
                                    <option value="">All</option>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group input-group-outline">
                                <label>Active</label>
                                <select class="form-control" wire:model.live="filters.active">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <label>&nbsp;&nbsp;</label><br>
                                <button wire:click="resetFilters" class="btn btn-outline-secondary w-100 mb-0">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            @if (session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Code @include('components.table.sort', ['field' => 'code'])</th>
                                        <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                        <th>Type @include('components.table.sort', ['field' => 'type'])</th>
                                        <th>Currency @include('components.table.sort', ['field' => 'currency'])</th>
                                        <th>Parent @include('components.table.sort', ['field' => 'parent_id'])</th>
                                        <th>Active @include('components.table.sort', ['field' => 'active'])</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accounts->where('parent_id', null) as $account)
                                        @include('accounting::livewire.accounts.partials.account-row', ['account' => $account, 'level' => 0])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $accounts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.partials.delete-confirmation-modal')
</div>
