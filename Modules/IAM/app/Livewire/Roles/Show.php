<?php

namespace Modules\IAM\Livewire\Roles;

use App\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Show extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $roleId;

    public $role;

    public $permissions = [];

    public $permissionsGrouped = [];

    public $selectedPermissions = [];

    public $search = '';

    public $selectAllPermissions = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount($roleId)
    {
        $this->roleId = $roleId;
        $this->role = Role::findOrFail($roleId);

        // Load already assigned permissions
        $this->selectedPermissions = $this->role->permissions()->pluck('name')->toArray();
    }

    public function updatedSelectAllPermissions($value)
    {
        if ($value) {
            $this->selectedPermissions = Permission::pluck('name')->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    public function savePermissions()
    {
        $this->role->syncPermissions($this->selectedPermissions);
        session()->flash('message', 'Permissions updated successfully.');
    }

    public function render()
    {
        $permissions = Permission::with('group')
            ->when($this->search, fn ($q) => $q->where('permissions.name', 'like', "%{$this->search}%"))
            ->leftJoin('permission_groups', 'permissions.permission_group_id', '=', 'permission_groups.id')
            ->select('permissions.*', 'permission_groups.name as group_name')
            ->orderBy('permission_groups.name')
            ->orderBy('permissions.name')
            ->get();

        $this->permissions = $permissions;

        return view('iam::livewire.roles.show', [
            'permissions' => $permissions,
        ]);
    }
}
