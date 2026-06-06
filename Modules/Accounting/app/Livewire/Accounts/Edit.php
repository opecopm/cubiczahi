<?php

namespace Modules\Accounting\Livewire\Accounts;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Accounting\Models\Account;

class Edit extends Component
{
    public int $accountId;

    public string $code = '';

    public string $name = '';

    public string $type = 'asset';

    public string $currency = 'SAR';

    public ?int $parent_id = null;

    public ?string $description = null;

    public bool $active = true;

    public function mount($accountId): void
    {
        $this->accountId = (int) $accountId;

        $account = Account::findOrFail($this->accountId);
        $this->code = (string) $account->code;
        $this->name = (string) $account->name;
        $this->type = (string) $account->type;
        $this->currency = (string) $account->currency;
        $this->parent_id = $account->parent_id ? (int) $account->parent_id : null;
        $this->description = $account->description;
        $this->active = (bool) $account->active;
    }

    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('accounts', 'code')->ignore($this->accountId)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Account::TYPE_SELECT))],
            'description' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'size:3'],
            'parent_id' => ['nullable', 'integer', 'exists:accounts,id', Rule::notIn([$this->accountId])],
            'active' => ['boolean'],
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $account = Account::findOrFail($this->accountId);
        $account->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'currency' => $validated['currency'],
            'parent_id' => $validated['parent_id'],
            'active' => (bool) $validated['active'],
        ]);

        session()->flash('message', 'Account updated successfully.');

        return redirect()->route('accounting.accounts.show', $account->id);
    }

    public function render()
    {
        $types = Account::TYPE_SELECT;
        $parentAccounts = Account::query()
            ->whereKeyNot($this->accountId)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return view('accounting::livewire.accounts.edit', compact('types', 'parentAccounts'));
    }
}
