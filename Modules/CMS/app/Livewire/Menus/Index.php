<?php

namespace Modules\CMS\Livewire\Menus;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Menu;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination, WithModalTrait, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    /** Translatable name array, keyed by locale e.g. ['en' => 'Main Menu', 'ar' => 'القائمة الرئيسية'] */
    public array $name = [];
    public $slug;
    public $menuId;

    public $updateMode  = false;
    public $activeLocale;

    protected $queryString = [
        'search'        => ['except' => ''],
        'sortDirection' => [],
        'perPage'       => [],
    ];

    protected function rules(): array
    {
        $rules = [
            'slug' => 'nullable|string|max:100',
        ];

        foreach ($this->activeLanguages() as $lang) {
            $required = $lang->is_default ? 'required' : 'nullable';
            $rules["name.{$lang->code}"] = "{$required}|string|max:255";
        }

        return $rules;
    }

    private function activeLanguages()
    {
        return \Modules\Global\Models\Language::where('status', 'active')->get();
    }

    public function mount(): void
    {
        $this->sortBy        = 'id';
        $this->sortDirection = 'desc';
        $this->perPage       = 100;
        $this->orderable     = ['id', 'name'];

        $languages          = $this->activeLanguages();
        $this->activeLocale = $languages->where('is_default', true)->first()?->code ?? 'en';

        foreach ($languages as $lang) {
            $this->name[$lang->code] = '';
        }
    }

    public function resetInputFields(): void
    {
        foreach ($this->activeLanguages() as $lang) {
            $this->name[$lang->code] = '';
        }
        $this->slug       = '';
        $this->menuId     = '';
        $this->updateMode = false;
    }

    public function filter(): void {}

    public function updatedName(): void
    {
        // Auto-generate slug from the default locale name when not in update mode
        if (!$this->updateMode) {
            $default    = $this->activeLanguages()->where('is_default', true)->first()?->code ?? 'en';
            $this->slug = Str::slug($this->name[$default] ?? '');
        }
    }

    public function store(): void
    {
        $this->validate();

        $default    = $this->activeLanguages()->where('is_default', true)->first()?->code ?? 'en';
        $this->slug = $this->slug ?: Str::slug($this->name[$default] ?? '');

        Menu::create([
            'name' => $this->name,
            'slug' => $this->slug,
        ]);

        session()->flash('message', 'Menu created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id): void
    {
        $languages = $this->activeLanguages();
        $menu      = Menu::findOrFail($id);

        $this->menuId     = $menu->id;
        $this->slug       = $menu->slug;
        $this->updateMode = true;
        $this->showModal  = true;

        foreach ($languages as $lang) {
            $this->name[$lang->code] = $menu->getTranslation('name', $lang->code, false) ?? '';
        }
    }

    public function update(): void
    {
        $this->validate();

        $menu = Menu::findOrFail($this->menuId);

        foreach ($this->activeLanguages() as $lang) {
            $menu->setTranslation('name', $lang->code, $this->name[$lang->code] ?? '');
        }

        if ($this->slug) {
            $menu->slug = Str::slug($this->slug);
        }

        $menu->save();

        session()->flash('message', 'Menu updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete(): void
    {
        Menu::find($this->deleteId)?->delete();
        session()->flash('message', 'Menu deleted successfully.');
    }

    public function render()
    {
        $activeLanguages = $this->activeLanguages();

        $menus = Menu::orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('cms::livewire.menus.index', compact('menus', 'activeLanguages'));
    }
}
