<?php

namespace Modules\Global\Livewire\GeneralDocumentTypes;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Global\Models\GeneralDocumentType;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $name;

    public $slug;

    public $description;

    public $status;

    public $typeId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'slug' => 'required|unique:general_document_types,slug,'.$this->typeId,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 10; // Default pagination limit
        $this->orderable = ['id', 'name', 'slug', 'status'];
        $this->status = 'active';
    }

    public function updatedName($value)
    {
        if (! $this->updateMode) {
            $this->slug = Str::slug($value);
        }
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->status = 'active';
        $this->typeId = '';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();
        GeneralDocumentType::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Document Type created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $type = GeneralDocumentType::findOrFail($id);
        $this->typeId = $type->id;
        $this->name = $type->name;
        $this->slug = $type->slug;
        $this->description = $type->description;
        $this->status = $type->status;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $type = GeneralDocumentType::find($this->typeId);
        $type->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Document Type updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        GeneralDocumentType::find($this->deleteId)->delete();
        session()->flash('message', 'Document Type deleted successfully.');
    }

    public function render()
    {
        $types = GeneralDocumentType::where('name', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('global::livewire.general-document-types.index', compact('types'));
    }
}
