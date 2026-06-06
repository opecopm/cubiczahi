<?php

namespace Modules\CRM\Livewire\Customers;

use App\Livewire\WithCountryStateCityTrait;
use App\Livewire\WithModalTrait;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CRM\Models\Customer;
use Modules\CRM\Models\CustomerGroup;
use Modules\Global\Models\Country;

class Edit extends Component
{
    use WithCountryStateCityTrait,WithFileUploads,WithModalTrait;

    public $name;

    public $email;

    public $phone;

    public $company = [];

    public $industry;

    public $website;

    public $trn;

    public $crn;

    public $customer_id;

    public $customer_group_id;

    public $customer;

    public $phone_code;

    public $secondaryLang;

    public $countries = [];

    public $newGroupName;

    public $newGroupParentId;

    public $showAddGroupModal = false;

    public function mount($customerId)
    {

        $customer = Customer::findOrFail($customerId);
        $this->customer = $customer;
        $this->secondaryLang = system_setting('secondary_language', 'ar');
        $this->customer_id = $customer->id;

        // Handle name (simple string)
        $this->name = $customer->name;
        if (is_string($this->name) && is_array(json_decode($this->name, true))) {
            $this->name = json_decode($this->name, true)['en'] ?? '';
        }

        // Handle company (translatable)
        $this->company = [
            'en' => $customer->getTranslation('company', 'en'),
            $this->secondaryLang => $customer->getTranslation('company', $this->secondaryLang),
        ];

        $this->email = $customer->email;

        // Handle phone splitting
        $this->countries = Country::all();
        $this->phone_code = $customer->phone_code;
        $this->phone = $customer->phone;

        $this->industry = $customer->industry;
        $this->website = $customer->website;
        $this->trn = $customer->trn;
        $this->customer_group_id = $customer->customer_group_id;

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

    public function update()
    {
        $this->validate();

        // Prepare translations
        $companyTranslations = [
            'en' => $this->company['en'] ?? null,
            $this->secondaryLang => $this->company[$this->secondaryLang] ?? ($this->company['en'] ?? null),
        ];

        $customer = Customer::updateOrCreate(
            ['id' => $this->customer_id],
            [
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
            ]
        );

        session()->flash('message', 'Customer saved successfully.');

        return redirect()->route('admin.crm.customers.show', ['customer' => $customer->id]);
    }

    protected function rules()
    {
        return [
            'name' => 'required',
            'company.en' => 'nullable',
            "company.{$this->secondaryLang}" => 'nullable',
            'email' => 'required',
            'phone' => 'required',
            'phone_code' => 'required',
            'industry' => 'nullable',
            'website' => 'nullable',
            'trn' => 'nullable',
            'crn' => 'nullable',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
        ];
    }

    public function render()
    {

        $groups = CustomerGroup::with('children')
            ->orderBy('name')
            ->get();

        return view('crm::livewire.customers.edit', compact('groups'));
    }
}
