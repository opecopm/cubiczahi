<?php

namespace Modules\IAM\Livewire\Roles;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $name;

    public $roleId;

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
            'name' => 'required|min:3|unique:roles,name,'.$this->roleId,
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'name'];

    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->roleId = '';
        $this->updateMode = false;
    }

    public function filter() {}

    public function store()
    {
        $this->validate();
        Role::create(['name' => $this->name]);
        session()->flash('message', 'Role created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $role = Role::find($this->roleId);
        $role->name = $this->name;
        $role->save();
        session()->flash('message', 'Role updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        Role::find($this->deleteId)->delete();
        session()->flash('message', 'Role deleted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $roles = Role::where('name', 'like', '%'.$this->search.'%')->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('iam::livewire.roles.index', compact('roles'));
    }
}
