<?php

namespace App\Livewire\Customer;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\Country;

class ProfileForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone_code = '';

    public string $phone = '';

    public bool $isEditing = false;

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_code = $user->phone_code ?? '';
        $this->phone = $user->phone ?? '';
    }

    #[Computed]
    public function countries()
    {
        return Country::where('status', 'active')
            ->orderBy('name')
            ->get(['name', 'iso2', 'phone_code']);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
            'phone_code' => ['nullable', 'required_with:phone', 'string', 'max:10'],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')
                    ->where('phone_code', $this->phone_code)
                    ->ignore(auth()->id()),
            ],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = auth()->user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_code' => $this->phone_code,
            'phone' => $this->phone,
        ]);

        if ($user->userable instanceof Customer) {
            $user->userable->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone_code' => $this->phone_code,
                'phone' => $this->phone,
            ]);
        }

        $this->isEditing = false;
        session()->flash('success', 'Profile updated successfully!');
    }

    public function toggleEdit()
    {
        $this->isEditing = ! $this->isEditing;
    }

    public function render()
    {
        return view('livewire.customer.profile-form', [
            'countries' => $this->countries,
        ]);
    }
}
