<?php

namespace Modules\Global\Livewire\Languages;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Global\Models\Language;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $languageId;

    public $name;

    public $code;

    public $status = 'active';

    public $is_default = false;

    public $direction = 'ltr';

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:20|unique:languages,code,'.$this->languageId,
            'status' => 'required|in:active,inactive',
            'is_default' => 'boolean',
            'direction' => 'required|in:ltr,rtl',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->orderable = ['id', 'name', 'code', 'status', 'is_default', 'direction'];
    }

    public function resetInputFields()
    {
        $this->languageId = null;
        $this->name = '';
        $this->code = '';
        $this->status = 'active';
        $this->is_default = false;
        $this->direction = 'ltr';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        if ($this->is_default) {
            Language::query()->update(['is_default' => false]);
        }

        Language::create([
            'name' => $this->name,
            'code' => strtolower($this->code),
            'status' => $this->status,
            'is_default' => (bool) $this->is_default,
            'direction' => $this->direction,
        ]);

        session()->flash('message', 'Language created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $lang = Language::findOrFail($id);
        $this->languageId = $lang->id;
        $this->name = $lang->name;
        $this->code = $lang->code;
        $this->status = $lang->status;
        $this->is_default = (bool) $lang->is_default;
        $this->direction = $lang->direction;

        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $lang = Language::findOrFail($this->languageId);

        if ($this->is_default) {
            Language::query()->where('id', '!=', $lang->id)->update(['is_default' => false]);
        }

        $lang->update([
            'name' => $this->name,
            'code' => strtolower($this->code),
            'status' => $this->status,
            'is_default' => (bool) $this->is_default,
            'direction' => $this->direction,
        ]);

        session()->flash('message', 'Language updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        Language::findOrFail($this->deleteId)->delete();
        $this->cancelDelete();
        session()->flash('message', 'Language deleted successfully.');
    }

    public function render()
    {
        $languages = Language::query()
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('global::livewire.languages.index', compact('languages'));
    }
}
