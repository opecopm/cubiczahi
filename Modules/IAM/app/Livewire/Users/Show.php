<?php

namespace Modules\IAM\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\Company;
use Modules\Business\Models\Location;
use Modules\CRM\Models\Customer;
use Spatie\Permission\Models\Role;

class Show extends Component
{
    use WithPagination;

    public $userId;

    public $user;

    public $customer;

    public $showCompanyModal = false;

    public $showLocationModal = false;

    public $showRoleModal = false;

    public $showDeleteModal = false;

    public $deleteId;

    public $deleteType;

    public $selectedCompanyId;

    public $selectedLocationId;

    public $selectedRoleName;

    public $isDefaultCompany = false;

    public function mount($id)
    {
        $this->userId = $id;
        $this->loadUserContext($id);
    }

    protected function loadUserContext($id): void
    {
        $this->user = User::with(['menu', 'companies', 'locations', 'roles', 'userable'])->findOrFail($id);

        $this->customer = null;

        if ($this->user->userable instanceof Customer) {
            $this->customer = $this->user->userable->loadMissing([
                'customerGroup',
            ]);
        }
    }

    public function updatedUserId()
    {
        return redirect()->route('admin.iam.users.show', $this->userId);
    }

    public function toggleCompanyModal()
    {
        $this->showCompanyModal = ! $this->showCompanyModal;
        $this->selectedCompanyId = null;
        $this->isDefaultCompany = false;
    }

    public function toggleLocationModal()
    {
        $this->showLocationModal = ! $this->showLocationModal;
        $this->selectedLocationId = null;
    }

    public function toggleRoleModal()
    {
        $this->showRoleModal = ! $this->showRoleModal;
        $this->selectedRoleName = null;
    }

    public function assignCompany()
    {
        $this->validate([
            'selectedCompanyId' => 'required|exists:companies,id',
        ]);

        $this->user->companies()->attach($this->selectedCompanyId, ['is_default' => $this->isDefaultCompany]);

        // If this is set as default, unset others if logic requires,
        // but for now let's just follow the pivot structure.
        // Actually, if is_default is true, we might want to ensure only one default.
        if ($this->isDefaultCompany) {
            $this->user->companies()->wherePivot('company_id', '!=', $this->selectedCompanyId)->updateExistingPivot($this->user->companies->pluck('id'), ['is_default' => false]);
        }

        $this->user->refresh();
        $this->showCompanyModal = false;
        session()->flash('message', 'Company assigned successfully.');
    }

    public function assignLocation()
    {
        $this->validate([
            'selectedLocationId' => 'required|exists:locations,id',
        ]);

        $this->user->locations()->syncWithoutDetaching([$this->selectedLocationId]);

        $this->user->refresh();
        $this->showLocationModal = false;
        session()->flash('message', 'Location assigned successfully.');
    }

    public function assignAdditionalRole()
    {
        $this->validate([
            'selectedRoleName' => 'required|exists:roles,name',
        ]);

        $this->user->assignRole($this->selectedRoleName);

        $this->user->refresh();
        $this->showRoleModal = false;
        $this->selectedRoleName = null;
        session()->flash('message', 'Role assigned successfully.');
    }

    public function confirmDelete($type, $id)
    {
        $this->deleteType = $type;
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteType = null;
        $this->deleteId = null;
    }

    public function executeDelete()
    {
        if ($this->deleteType === 'company') {
            $this->removeCompany($this->deleteId);
        } elseif ($this->deleteType === 'location') {
            $this->removeLocation($this->deleteId);
        }
        $this->cancelDelete();
    }

    public function removeCompany($companyId)
    {
        $this->user->companies()->detach($companyId);
        $this->user->refresh();
        session()->flash('message', 'Company removed successfully.');
    }

    public function removeLocation($locationId)
    {
        $this->user->locations()->detach($locationId);
        $this->user->refresh();
        session()->flash('message', 'Location removed successfully.');
    }

    public function removeRole($roleName)
    {
        $this->user->removeRole($roleName);
        $this->user->refresh();
        session()->flash('message', 'Role removed successfully.');
    }

    public function render()
    {
        $users = User::orderBy('first_name', 'asc')->get();

        $availableCompanies = Company::whereNotIn('id', $this->user->companies->pluck('id'))->get();
        $availableLocations = Location::whereNotIn('id', $this->user->locations->pluck('id'))->orderBy('name')->get();
        $availableRoles = Role::query()
            ->where('guard_name', config('auth.defaults.guard'))
            ->whereNotIn('name', $this->user->getRoleNames()->toArray())
            ->orderBy('name')
            ->get();

        return view('iam::livewire.users.show', compact('users', 'availableCompanies', 'availableLocations', 'availableRoles'));
    }
}
