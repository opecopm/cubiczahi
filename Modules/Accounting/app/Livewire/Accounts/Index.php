<?php

namespace Modules\Accounting\Livewire\Accounts;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Accounting\Models\Account;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public string $search = '';

    public array $filters = [
        'type' => '',
        'active' => '',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filters' => ['except' => []],
        'sortDirection' => [],
        'perPage' => [],
    ];

    public function mount(): void
    {
        $this->sortBy = 'code';
        $this->sortDirection = 'asc';
        $this->perPage = 100;
        $this->orderable = ['id', 'code', 'name', 'type', 'currency', 'parent_id', 'active'];
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'type' => '',
            'active' => '',
        ];
    }

    public function delete(): void
    {
        Account::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Account deleted successfully.');
        $this->cancelDelete();
    }

    public function render()
    {
        $queryBase = Account::query();
        $accountsCount = (clone $queryBase)->count();
        $activeAccountsCount = (clone $queryBase)->where('active', true)->count();
        $inactiveAccountsCount = (clone $queryBase)->where('active', false)->count();

        $accounts = Account::query()
            ->with('parent')
            ->when(($this->filters['type'] ?? '') !== '', function ($query) {
                $query->where('type', $this->filters['type']);
            })
            ->when(($this->filters['active'] ?? '') !== '', function ($query) {
                $query->where('active', $this->filters['active'] === '1');
            })
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $types = Account::TYPE_SELECT;

        return view('accounting::livewire.accounts.index', compact('accounts', 'types', 'accountsCount', 'activeAccountsCount', 'inactiveAccountsCount'));
    }
}
