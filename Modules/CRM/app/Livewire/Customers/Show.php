<?php

namespace Modules\CRM\Livewire\Customers;

use App\Livewire\WithCountryStateCityTrait;
use App\Livewire\WithModalTrait;
use Livewire\Component;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\Country;
use Modules\Global\Models\State;

class Show extends Component
{
    use WithCountryStateCityTrait, WithModalTrait;

    public $customer;

    public $status;

    public $address_type;

    public $line1;

    public $line2;

    public $postal_code;

    public $addresses;

    public $addressEditId = null;

    public $showAddressEditModal = false;

    public function mount($customerId)
    {
        $this->initializeWithCountryStateCityTrait(); // Initialize countries
        $this->customer = Customer::with(['customerGroup', 'addresses', 'generalDocuments'])->findOrFail($customerId);
        $this->status = $this->customer->status;
    }

    public function updateStatus()
    {
        $this->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $this->customer->update([
            'status' => $this->status,
        ]);

        $this->dispatch('alert',
            type: 'success',
            message: 'Customer status updated successfully!'
        );
        $this->dispatch('close-modal');
    }

    public function resetStatus()
    {
        $this->status = $this->customer->status;
    }

    public function editAddress($id)
    {
        $address = $this->customer->addresses()->find($id);
        if ($address) {
            $this->addressEditId = $id;
            $this->loadAddress($address);
            $this->showAddressEditModal = true;
        }
    }

    public function loadAddress($address)
    {
        $this->address_type = $address->address_type;

        $this->country = $address->country;
        $country = Country::where('name', $this->country)->first();
        $this->states = $country ? $country->states()->get() : [];
        $this->state = $address->state;

        $state = State::where('name', $this->state)->first();
        $this->cities = $state ? $state->cities()->get() : [];
        $this->city = $address->city;
        $this->line1 = $address->line1;
        $this->line2 = $address->line2;
        $this->postal_code = $address->postal_code;
    }

    public function updateAddress()
    {
        $this->validate([
            'address_type' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'line1' => 'required',
            'postal_code' => 'required',
        ]);

        $address = $this->customer->addresses()->find($this->addressEditId);
        if ($address) {
            $address->update([
                'address_type' => $this->address_type,
                'country' => $this->country,
                'state' => $this->state,
                'city' => $this->city,
                'line1' => $this->line1,
                'line2' => $this->line2,
                'postal_code' => $this->postal_code,
            ]);
        }
        $this->customer->refresh();
        $this->closeAddressEditModal();
    }

    public function deleteAddress($id)
    {
        $this->customer->addresses()->where('id', $id)->delete();
        $this->customer->refresh();
    }

    public function closeAddressEditModal()
    {
        $this->showAddressEditModal = false;
        $this->reset(['addressEditId', 'address_type', 'country', 'state', 'city', 'line1', 'line2', 'postal_code']);
        $this->initializeWithCountryStateCityTrait();
    }

    public function storeAddress()
    {
        $this->validate([
            'address_type' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'line1' => 'required',
            'postal_code' => 'required',
        ]);

        $address = $this->customer->addresses()->create([
            'address_type' => $this->address_type,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'postal_code' => $this->postal_code,
        ]);
        $this->customer->refresh();
        $this->closeModal();
    }

    public function render()
    {

        return view('crm::livewire.customers.show');
    }
}
