<?php

namespace Modules\Global\Livewire\ReferenceSchemas;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Global\Models\ReferenceSchema;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $type;

    public $model;

    public $prefix;

    public $date_prefix;

    public $reset_period;

    public $initial_value;

    public $increment;

    public $next_value;

    public $digits;

    public $status;

    public $schemaId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'type' => 'required|min:3|unique:reference_schemas,type,'.$this->schemaId,
            'model' => 'nullable|string|unique:reference_schemas,model,'.$this->schemaId,
            'prefix' => 'nullable|string',
            'date_prefix' => 'nullable|string',
            'reset_period' => 'nullable|in:none,daily,monthly,yearly',
            'initial_value' => 'required|numeric|min:0',
            'increment' => 'required|numeric|min:1',
            'next_value' => 'required',
            'digits' => 'required',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'type', 'status'];
    }

    public function resetInputFields()
    {
        $this->type = '';
        $this->model = '';
        $this->prefix = '';
        $this->date_prefix = '';
        $this->reset_period = 'monthly';
        $this->initial_value = '';
        $this->increment = '';
        $this->next_value = '';
        $this->digits = '';
        $this->status = 'active';
        $this->schemaId = '';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();
        ReferenceSchema::create([
            'type' => $this->type,
            'model' => $this->model,
            'prefix' => $this->prefix,
            'date_prefix' => $this->date_prefix,
            'reset_period' => $this->reset_period,
            'initial_value' => $this->initial_value,
            'increment' => $this->increment,
            'next_value' => $this->next_value,
            'digits' => $this->digits,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Reference Schema created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $schema = ReferenceSchema::findOrFail($id);
        $this->schemaId = $schema->id;
        $this->type = $schema->type;
        $this->model = $schema->model;
        $this->prefix = $schema->prefix;
        $this->date_prefix = $schema->date_prefix;
        $this->reset_period = $schema->reset_period;
        $this->initial_value = $schema->initial_value;
        $this->increment = $schema->increment;
        $this->next_value = $schema->next_value;
        $this->digits = $schema->digits;
        $this->status = strtolower($schema->status);
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $schema = ReferenceSchema::find($this->schemaId);
        $schema->update([
            'type' => $this->type,
            'model' => $this->model,
            'prefix' => $this->prefix,
            'date_prefix' => $this->date_prefix,
            'reset_period' => $this->reset_period,
            'initial_value' => $this->initial_value,
            'increment' => $this->increment,
            'next_value' => $this->next_value,
            'digits' => $this->digits,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Reference Schema updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        ReferenceSchema::find($this->deleteId)->delete();
        session()->flash('message', 'Reference Schema deleted successfully.');
    }

    public function render()
    {
        $schemas = ReferenceSchema::where('type', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('global::livewire.reference-schemas.index', compact('schemas'));
    }
}
