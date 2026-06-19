<?php

namespace Modules\CRM\Livewire\Customers;

use App\Livewire\WithCountryStateCityTrait;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CRM\Models\Customer;
use Modules\CRM\Models\CustomerGroup;
use Modules\Global\Models\Country;
use Modules\Global\Models\State;

class Create extends Component
{
    use WithCountryStateCityTrait, WithFileUploads;

    public $name;

    public $email;

    public $phone;

    public $company = [];

    public $industry;

    public $website;

    public $trn;

    public $crn;

    public $customer_group_id;

    public $phone_code;

    public $address_type = 'billing_address';

    public $line1;

    public $line2;

    public $postal_code;

    // Shipping Address
    public $same_as_billing = false;

    public $shipping_line1;

    public $shipping_line2;

    public $shipping_postal_code;

    public $shipping_country;

    public $shipping_state;

    public $shipping_city;

    public $shipping_states = [];

    public $shipping_cities = [];

    public array $activeLanguages = [];

    public $newGroupName;

    public $newGroupParentId;

    public $showAddGroupModal = false;

    public function mount()
    {
        $langs = system_setting('active_languages', ['ar']);
        $this->activeLanguages = is_string($langs) ? (json_decode($langs, true) ?? [$langs]) : $langs;
        $this->initializeWithCountryStateCityTrait();
    }

    public function updatedShippingCountry($value)
    {
        $country = Country::where('name', $value)->first();
        $this->shipping_states = $country ? $country->states : [];
        $this->shipping_state = null;
        $this->shipping_cities = [];
    }

    public function updatedShippingState($value)
    {
        $state = State::where('name', $value)->first();
        $this->shipping_cities = $state ? $state->cities : [];
        $this->shipping_city = null;
    }

    public function openAddGroupModal()
    {
        $this->showAddGroupModal = true;
    }

    public function saveNewGroup()
    {
        $this->validate([
            'newGroupName' => 'required|min:2|unique:customer_groups,name',
            'newGroupParentId' => 'nullable|exists:customer_groups,id',
        ]);

        $group = CustomerGroup::create([
            'name' => $this->newGroupName,
            'parent_id' => $this->newGroupParentId,
        ]);

        $this->customer_group_id = $group->id;
        $this->newGroupName = '';
        $this->newGroupParentId = null;
        $this->showAddGroupModal = false;
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required',
            'company.en' => 'nullable',
            'email' => 'email|nullable',
            'phone' => 'required',
            'phone_code' => 'required',
            'company' => 'nullable',
            'industry' => 'nullable',
            'website' => 'nullable',
            'trn' => 'nullable',
            'crn' => 'nullable',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'line1' => 'nullable|string',
            'line2' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'same_as_billing' => 'boolean',
            'shipping_line1' => 'nullable|required_if:same_as_billing,false|string',
            'shipping_line2' => 'nullable|string',
            'shipping_postal_code' => 'nullable|string',
            'shipping_country' => 'nullable|required_if:same_as_billing,false|string',
            'shipping_state' => 'nullable|string',
            'shipping_city' => 'nullable|string',
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $rules["company.{$lang}"] = 'nullable';
            }
        }
        return $rules;
    }

    public function store()
    {

        $this->validate();

        // Prepare translations
        $companyTranslations = [
            'en' => $this->company['en'] ?? null,
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $companyTranslations[$lang] = $this->company[$lang] ?? ($this->company['en'] ?? null);
            }
        }

        $customer = Customer::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_code' => $this->phone_code,
            'phone' => $this->phone,
            'company' => $companyTranslations,
            'industry' => $this->industry,
            'website' => $this->website,
            'trn' => $this->trn,
            'crn' => $this->crn,
            'customer_group_id' => $this->customer_group_id,
        ]);

        if ($this->line1 || $this->country || $this->city) {
            $customer->addresses()->create([
                'address_type' => 'billing_address',
                'country' => $this->country,
                'state' => $this->state,
                'city' => $this->city,
                'line1' => $this->line1,
                'line2' => $this->line2,
                'postal_code' => $this->postal_code,
            ]);
        }

        if ($this->same_as_billing) {
            if ($this->line1 || $this->country || $this->city) {
                $customer->addresses()->create([
                    'address_type' => 'shipping_address',
                    'country' => $this->country,
                    'state' => $this->state,
                    'city' => $this->city,
                    'line1' => $this->line1,
                    'line2' => $this->line2,
                    'postal_code' => $this->postal_code,
                ]);
            }
        } else {
            if ($this->shipping_line1 || $this->shipping_country || $this->shipping_city) {
                $customer->addresses()->create([
                    'address_type' => 'shipping_address',
                    'country' => $this->shipping_country,
                    'state' => $this->shipping_state,
                    'city' => $this->shipping_city,
                    'line1' => $this->shipping_line1,
                    'line2' => $this->shipping_line2,
                    'postal_code' => $this->shipping_postal_code,
                ]);
            }
        }

        session()->flash('message', 'Customer created successfully.');

        return redirect()->route('admin.crm.customers.show', ['customer' => $customer->id]);

    }

    public function render()
    {
        $groups = CustomerGroup::with('children')
            ->orderBy('name')
            ->get();

        return view('crm::livewire.customers.create', compact('groups'));
    }
}
