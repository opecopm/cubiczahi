<?php

namespace Modules\Accounting\Livewire\Accounts;

use App\Livewire\WithModalTrait;
use Livewire\Component;
use Modules\Accounting\Models\Account;

class Show extends Component
{
    use WithModalTrait;

    public Account $account;

    public function mount($accountId): void
    {
        $this->account = Account::query()
            ->with('parent')
            ->findOrFail((int) $accountId);
    }

    public function openDeleteModal(): void
    {
        $this->deleteId = $this->account->getKey();
    }

    public function delete()
    {
        $this->account->delete();

        return redirect()
            ->route('accounting.accounts.index')
            ->with('message', 'Account deleted successfully.');
    }

    public function render()
    {
        return view('accounting::livewire.accounts.show');
    }
}
