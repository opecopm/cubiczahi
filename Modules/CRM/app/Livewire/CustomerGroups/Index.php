<?php

namespace Modules\CRM\Livewire\CustomerGroups;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\CRM\Models\CustomerGroup;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $groupId;

    public $updateMode = false;

    public $name;

    public $parent_id;

    public $parents = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:customer_groups,name,'.$this->groupId,
            'parent_id' => 'nullable|exists:customer_groups,id',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->orderable = ['id', 'name'];
        $this->parents = CustomerGroup::with('children')->whereNull('parent_id')->orWhere('parent_id', 0)->get();
    }

    public function resetForm()
    {
        $this->groupId = null;
        $this->name = '';
        $this->parent_id = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        CustomerGroup::create([
            'name' => $this->name,
            'parent_id' => $this->parent_id,
        ]);

        session()->flash('message', 'Customer Group created successfully.');
        $this->closeModal();
        $this->resetForm();
        $this->parents = CustomerGroup::with('children')->whereNull('parent_id')->orWhere('parent_id', 0)->get();
    }

    public function edit($id)
    {
        $group = CustomerGroup::findOrFail($id);
        $this->groupId = $group->id;
        $this->name = $group->name;
        $this->parent_id = $group->parent_id;

        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $group = CustomerGroup::findOrFail($this->groupId);
        $group->update([
            'name' => $this->name,
            'parent_id' => $this->parent_id,
        ]);

        session()->flash('message', 'Customer Group updated successfully.');
        $this->closeModal();
        $this->resetForm();
        $this->parents = CustomerGroup::with('children')->whereNull('parent_id')->orWhere('parent_id', 0)->get();
    }

    public function delete()
    {
        CustomerGroup::findOrFail($this->deleteId)->delete();
        $this->deleteId = '';
        session()->flash('message', 'Customer Group deleted successfully.');
        $this->parents = CustomerGroup::with('children')->whereNull('parent_id')->orWhere('parent_id', 0)->get();
    }

    public function render()
    {
        $groups = CustomerGroup::query()
            ->with(['parent', 'children'])
            ->where('name', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('crm::livewire.customergroups.index', compact('groups'));
    }
}
