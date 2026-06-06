<?php

namespace Modules\Global\Livewire\CustomFields;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Global\Models\CustomField;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $fieldId;

    public $module;

    public $model;

    public $name;

    public $type;

    public $options;

    public $is_required = false;

    public $show_in_list = false;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'module' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z]+$/', // only letters, no spaces
            ],
            'model' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z]+$/', // only letters, no spaces
            ],
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z_]+$/', // letters and underscores only
                'unique:custom_fields,name,'.$this->fieldId,
            ],
            'type' => 'required|string|in:text,textarea,select,checkbox,radio,date,number',
            'options' => 'nullable|string',
            'is_required' => 'boolean',
            'show_in_list' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->orderable = ['id', 'name', 'module', 'model', 'type'];
    }

    public function resetInputFields()
    {
        $this->fieldId = null;
        $this->module = '';
        $this->model = '';
        $this->name = '';
        $this->type = 'text';
        $this->options = '';
        $this->is_required = false;
        $this->show_in_list = false;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        CustomField::create([
            'module' => $this->module,
            'model' => $this->model,
            'name' => strtolower($this->name),
            'type' => $this->type,
            'options' => $this->options,
            'is_required' => (bool) $this->is_required,
            'show_in_list' => (bool) $this->show_in_list,
        ]);

        session()->flash('message', 'Custom Field created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $field = CustomField::findOrFail($id);
        $this->fieldId = $field->id;
        $this->module = $field->module;
        $this->model = $field->model;
        $this->name = $field->name;
        $this->type = $field->type;
        $this->options = $field->options;
        $this->is_required = $field->is_required;
        $this->show_in_list = $field->show_in_list;

        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $field = CustomField::findOrFail($this->fieldId);
        $field->update([
            'module' => $this->module,
            'model' => $this->model,
            'name' => strtolower($this->name),
            'type' => $this->type,
            'options' => $this->options,
            'is_required' => (bool) $this->is_required,
            'show_in_list' => (bool) $this->show_in_list,
        ]);

        session()->flash('message', 'Custom Field updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        CustomField::findOrFail($this->deleteId)->delete();
        $this->closeModal();
        session()->flash('message', 'Custom Field deleted successfully.');
    }

    public function render()
    {
        $modules = CustomField::moduleSelect();
        $fields = CustomField::query()
            ->where('name', 'like', '%'.$this->search.'%')
            ->orWhere('module', 'like', '%'.$this->search.'%')
            ->orWhere('model', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('global::livewire.custom-fields.index', compact('fields', 'modules'));
    }
}
