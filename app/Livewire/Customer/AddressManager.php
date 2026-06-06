<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Modules\Global\Models\Country;

class AddressManager extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $address_type = 'delivery_address';

    public string $country = '';

    public string $state = '';

    public string $city = '';

    public string $line1 = '';

    public string $line2 = '';

    public string $postal_code = '';

    protected function rules(): array
    {
        return [
            'address_type' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:30'],
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $addressId): void
    {
        $address = $this->customer()->addresses()->findOrFail($addressId);

        $this->editingId = $address->id;
        $this->address_type = $address->address_type ?? 'delivery_address';
        $this->country = $address->country ?? '';
        $this->state = $address->state ?? '';
        $this->city = $address->city ?? '';
        $this->line1 = $address->line1 ?? '';
        $this->line2 = $address->line2 ?? '';
        $this->postal_code = $address->postal_code ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $customer = $this->customer();

        if ($this->editingId) {
            $customer->addresses()->findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Address updated successfully.');
        } else {
            $customer->addresses()->create($data);
            session()->flash('success', 'Address added successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $addressId): void
    {
        $this->customer()->addresses()->where('id', $addressId)->delete();
        session()->flash('success', 'Address deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'country',
            'state',
            'city',
            'line1',
            'line2',
            'postal_code',
        ]);

        $this->address_type = 'delivery_address';
        $this->showForm = false;
        $this->resetValidation();
    }

    private function customer()
    {
        return auth()->user()->ensureCustomerProfile();
    }

    public function render()
    {
        $customer = $this->customer();

        return view('livewire.customer.address-manager', [
            'addresses' => $customer->addresses()->latest()->get(),
            'countries' => Country::orderBy('name')->get(['name']),
        ]);
    }
}
