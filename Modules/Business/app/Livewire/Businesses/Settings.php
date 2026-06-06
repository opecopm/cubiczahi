<?php

namespace Modules\Business\Livewire\Businesses;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\BusinessSetting;

class Settings extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage = 15;

    public string $search = '';

    public $key = '';

    public $value = '';

    public $settingId;

    public $updateMode = false;

    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'key'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
        'filters' => ['except' => []],
    ];

    protected function rules()
    {
        $id = $this->settingId;

        return [
            'key' => 'required|string|max:255|unique:business_settings,key,'.$id,
            'value' => 'required|string',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'key';
        $this->sortDirection = 'asc';
        $this->perPage = 15;
        $this->orderable = ['id', 'key', 'value'];
        $this->model = new BusinessSetting;
        $this->initFilters($this->model);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $this->key = '';
        $this->value = '';
        $this->settingId = null;
        $this->updateMode = false;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->updateMode = false;
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        BusinessSetting::create([
            'key' => $this->key,
            'value' => $this->value,
        ]);

        session()->flash('message', 'Business setting created successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function edit($id)
    {
        $setting = BusinessSetting::findOrFail($id);

        $this->settingId = $setting->id;
        $this->key = $setting->key;
        $this->value = $setting->value;
        $this->updateMode = true;
        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        $setting = BusinessSetting::findOrFail($this->settingId);

        $setting->update([
            'key' => $this->key,
            'value' => $this->value,
        ]);

        session()->flash('message', 'Business setting updated successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function delete()
    {
        BusinessSetting::findOrFail($this->deleteId)->delete();
        $this->closeModal();
        session()->flash('message', 'Business setting deleted successfully.');
    }

    public function render()
    {
        $baseQuery = BusinessSetting::query();

        $settingsCount = $baseQuery->clone()->count();

        $query = $baseQuery->clone();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('key', 'like', '%'.$this->search.'%')
                    ->orWhere('value', 'like', '%'.$this->search.'%');
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $settings = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('business::livewire.businesses.settings', [
            'settings' => $settings,
            'settingsCount' => $settingsCount,
            'model' => $this->model,
        ]);
    }
}
