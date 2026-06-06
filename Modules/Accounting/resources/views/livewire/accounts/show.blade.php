<div class="container-fluid py-4">
    <x-page.page-header
        title="Account Details"
        :breadcrumbs="[
            ['label' => 'Accounts', 'url' => route('accounting.accounts.index')],
            ['label' => $account->code, 'url' => null],
        ]"
        :actions="[
            [
                'label' => 'Back',
                'icon' => 'arrow_back',
                'url' => route('accounting.accounts.index'),
                'class' => 'bg-gradient-light'
            ],
            [
                'label' => 'Edit',
                'icon' => 'edit',
                'url' => route('accounting.accounts.edit', ['account' => $account->id]),
                'class' => 'bg-gradient-dark'
            ],
            [
                'label' => 'Delete',
                'icon' => 'close',
                'action' => 'openDeleteModal',
                'class' => 'bg-gradient-danger'
            ],
        ]"
    />

    <div class="row content">
        <div class="col-12">
            @if (session()->has('message'))
                <div class="alert alert-success text-white">
                    {{ session('message') }}
                </div>
            @endif

            <div class="card my-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong>Code</strong>
                            <div>{{ $account->code }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Name</strong>
                            <div>{{ $account->name }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Type</strong>
                            <div>{{ \Modules\Accounting\Models\Account::TYPE_SELECT[$account->type] ?? $account->type }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong>Currency</strong>
                            <div>{{ $account->currency }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Parent</strong>
                            <div>
                                @if ($account->parent)
                                    {{ $account->parent->code }} - {{ $account->parent->name }}
                                @else
                                    None
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Active</strong>
                            <div>{{ $account->active ? 'Yes' : 'No' }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description</strong>
                        <div>{{ $account->description }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.partials.delete-confirmation-modal')
</div>

