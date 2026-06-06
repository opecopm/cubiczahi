<?php

namespace Modules\Accounting\Livewire\Accounts;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Accounting\Models\Account;

class Create extends Component
{
    public string $code = '';

    public string $name = '';

    public string $type = 'asset';

    public string $currency = 'SAR';

    public ?int $parent_id = null;

    public ?string $description = null;

    public bool $active = true;

    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Account::TYPE_SELECT))],
            'description' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'size:3'],
            'parent_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'active' => ['boolean'],
        ];
    }

    public function store()
    {
        $validated = $this->validate();

        $account = Account::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'currency' => $validated['currency'],
            'parent_id' => $validated['parent_id'],
            'active' => (bool) $validated['active'],
        ]);

        session()->flash('message', 'Account created successfully.');

        return redirect()->route('accounting.accounts.show', $account->id);
    }

    public function render()
    {
        $types = Account::TYPE_SELECT;
        $parentAccounts = Account::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return view('accounting::livewire.accounts.create', compact('types', 'parentAccounts'));
    }
}
