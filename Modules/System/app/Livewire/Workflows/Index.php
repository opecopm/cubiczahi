<?php

namespace Modules\System\Livewire\Workflows;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\Company;
use Modules\Business\Models\Location;
use Modules\System\Models\Workflow;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $filters = [];

    public $name;

    public $model_type;

    public $company_id;

    public $location_id;

    public $description;

    public $is_active = true;

    public $workflowId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filters' => ['except' => []],
        'sortDirection' => [],
        'perPage' => [],
    ];

    public function resetFilters()
    {
        $this->reset(['filters', 'search']);
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'model_type' => 'required',
            'company_id' => 'nullable',
            'location_id' => 'nullable',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->orderable = ['id', 'name', 'model_type', 'is_active'];
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->model_type = '';
        $this->company_id = null;
        $this->location_id = null;
        $this->description = '';
        $this->is_active = true;
        $this->workflowId = '';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();
        Workflow::create([
            'name' => $this->name,
            'model_type' => $this->model_type,
            'company_id' => $this->company_id,
            'location_id' => $this->location_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
        session()->flash('message', 'Workflow created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $workflow = Workflow::findOrFail($id);
        $this->workflowId = $workflow->id;
        $this->name = $workflow->name;
        $this->model_type = $workflow->model_type;
        $this->company_id = $workflow->company_id;
        $this->location_id = $workflow->location_id;
        $this->description = $workflow->description;
        $this->is_active = $workflow->is_active;

        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $workflow = Workflow::find($this->workflowId);
        $workflow->update([
            'name' => $this->name,
            'model_type' => $this->model_type,
            'company_id' => $this->company_id,
            'location_id' => $this->location_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
        session()->flash('message', 'Workflow updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        Workflow::find($this->deleteId)->delete();
        session()->flash('message', 'Workflow deleted successfully.');
    }

    public function render()
    {
        $query = Workflow::with(['company', 'location']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('model_type', 'like', '%'.$this->search.'%');
            });
        }

        foreach ($this->filters as $field => $value) {
            if ($value !== '' && $value !== null) {
                $query->where($field, $value);
            }
        }

        $workflows = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('system::livewire.workflows.index', [
            'workflows' => $workflows,
            'model' => new Workflow,
            // Pass these for the creation modal
            'companies' => Company::all(),
            'locations' => Location::all(),
        ]);
    }
}
