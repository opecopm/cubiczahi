<?php

namespace Modules\IAM\Livewire\PermissionGroups;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use App\Models\PermissionGroup;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $name;

    public $groupId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:permission_groups,name,'.$this->groupId,
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'name'];
    }

    public function filter() {}

    public function resetInputFields()
    {
        $this->name = '';
        $this->groupId = '';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();
        PermissionGroup::create(['name' => $this->name]);
        session()->flash('message', 'Permission Group created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $group = PermissionGroup::findOrFail($id);
        $this->groupId = $group->id;
        $this->name = $group->name;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $group = PermissionGroup::find($this->groupId);
        $group->name = $this->name;
        $group->save();
        session()->flash('message', 'Permission Group updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        PermissionGroup::find($this->deleteId)->delete();
        session()->flash('message', 'Permission Group deleted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $groups = PermissionGroup::where('name', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('iam::livewire.permission-groups.index', compact('groups'));
    }
}
