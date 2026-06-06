<?php

namespace Modules\IAM\Livewire\Permissions;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use App\Models\PermissionGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $name;

    public $permissionId;

    public $permissionGroupId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
        'search' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:permissions,name,'.$this->permissionId,
            'permissionGroupId' => 'required|exists:permission_groups,id',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'permissions.id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['permissions.id', 'permissions.name', 'permission_groups.name'];
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->permissionId = '';
        $this->permissionGroupId = '';
        $this->updateMode = false;
    }

    public function filter() {}

    public function store()
    {
        $this->validate();
        Permission::create(['name' => $this->name, 'permission_group_id' => $this->permissionGroupId]);
        session()->flash('message', 'Permission created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $this->permissionId = $permission->id;
        $this->name = $permission->name;
        $this->permissionGroupId = $permission->permission_group_id;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $permission = Permission::find($this->permissionId);
        $permission->name = $this->name;
        $permission->permission_group_id = $this->permissionGroupId;
        $permission->save();
        session()->flash('message', 'Permission updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        Permission::find($this->deleteId)->delete();
        session()->flash('message', 'Permission deleted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $permissions = Permission::join('permission_groups', 'permissions.permission_group_id', '=', 'permission_groups.id')
            ->where('permissions.name', 'like', '%'.$this->search.'%')
            ->orWhere('permission_groups.name', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->select('permissions.*', 'permission_groups.name as group_name')
            ->paginate($this->perPage);
        $permissionGroups = PermissionGroup::orderBy('name')->get();

        return view('iam::livewire.permissions.index', compact('permissions', 'permissionGroups'));
    }
}
