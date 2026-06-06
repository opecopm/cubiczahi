<?php

namespace Modules\CMS\Livewire\BlogCategories;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Modules\CMS\Models\BlogCategory;
use Modules\Global\Models\Language;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class Index extends Component
{
    public array $languages = [];
    public ?string $defaultLocale = null;

    public $search = '';
    public $categories; // use collection

    // Form state
    public $showModal = false;
    public $editMode = false;
    public $categoryId = null;
    public $parentId = null;
    public $name = [];
    public $slug = '';
    public $manualSlug = false;
    public $status = true;

    public $confirmingDeleteId = null;

    protected $listeners = [
        'refreshCategories' => 'loadCategories',
    ];

    public function mount(): void
    {
        $this->loadLanguages();
        $this->loadCategories();
    }

    protected function loadLanguages(): void
    {
        $active = Language::query()->where('status', true)->orderByDesc('is_default')->orderBy('name')->get();
        $this->languages = $active->map(fn ($l) => [
            'code' => $l->code,
            'name' => $l->name,
            'direction' => $l->direction,
            'is_default' => (bool) $l->is_default,
        ])->toArray();
        $this->defaultLocale = collect($this->languages)->firstWhere('is_default', true)['code'] ?? (App::getLocale() ?: 'en');
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'slug') {
            $this->manualSlug = true;
            return;
        }

        $default = $this->defaultLocale ?? App::getLocale();
        if ($propertyName === "name.$default" && !$this->manualSlug) {
            $this->slug = Str::slug($this->name[$default] ?? '');
        }
    }

    public function updatedSearch(): void
    {
        $this->loadCategories();
    }

    public function loadCategories(): void
    {
        $locale = App::getLocale();
        $query = BlogCategory::query()->with('children');
        if ($this->search) {
            $query->where("name->{$locale}", 'like', '%' . $this->search . '%');
        }
        $this->categories = $query->whereNull('parent_id')->orderBy('id', 'asc')->get();
    }

    public function openCreateModal(?int $parentId = null): void
    {
        $this->resetForm();
        $this->editMode = false;
        $this->parentId = $parentId;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $this->editMode = true;
        $cat = BlogCategory::findOrFail($id);
        $this->categoryId = $cat->id;
        $this->parentId = $cat->parent_id;
        $this->slug = $cat->slug;
        $this->status = (bool) ($cat->status ?? true);

        foreach ($this->languages as $lang) {
            $code = $lang['code'];
            $this->name[$code] = $cat->getTranslation('name', $code);
        }
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate($this->rules());
        if ($this->editMode) {
            $this->updateCategory();
        } else {
            $this->createCategory();
        }
        $this->showModal = false;
        $this->dispatch('refreshCategories');
    }

    protected function rules(): array
    {
        $rules = [
            'parentId' => ['nullable', Rule::exists('cms_blog_categories', 'id')],
            'slug' => ['nullable', 'string', 'max:255'],
            'status' => ['boolean'],
        ];
        $default = $this->defaultLocale ?? 'en';
        $rules["name.$default"] = ['required', 'string', 'max:255'];
        foreach ($this->languages as $lang) {
            $code = $lang['code'];
            if (!isset($rules["name.$code"])) {
                $rules["name.$code"] = ['nullable', 'string', 'max:255'];
            }
        }
        return $rules;
    }

    protected function createCategory(): void
    {
        $cat = new BlogCategory();
        $cat->parent_id = $this->parentId;
        $cat->slug = $this->slug ?: null;
        $cat->status = $this->status ? 1 : 0;
        // Set translations
        foreach ($this->languages as $lang) {
            $code = $lang['code'];
            $val = $this->name[$code] ?? null;
            if ($val) {
                $cat->setTranslation('name', $code, $val);
            }
        }
        // Fallback to default if no names provided
        if (empty($cat->getTranslations('name'))) {
            $default = $this->defaultLocale ?? 'en';
            $cat->setTranslation('name', $default, '');
        }
        $cat->save();
    }

    protected function updateCategory(): void
    {
        $cat = BlogCategory::findOrFail($this->categoryId);
        $cat->parent_id = $this->parentId;
        $cat->slug = $this->slug ?: null;
        $cat->status = $this->status ? 1 : 0;
        $translations = [];
        foreach ($this->languages as $lang) {
            $code = $lang['code'];
            $val = $this->name[$code] ?? null;
            if ($val !== null) {
                $translations[$code] = $val;
            }
        }
        if (!empty($translations)) {
            $cat->setTranslations('name', $translations);
        }
        $cat->save();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function delete(): void
    {
        if (!$this->confirmingDeleteId) return;
        $cat = BlogCategory::with('children', 'blogs')->findOrFail($this->confirmingDeleteId);
        if ($cat->children->count() > 0) {
            $this->addError('delete', 'Cannot delete a category that has children.');
            return;
        }
        // Detach related blogs
        $cat->blogs()->detach();
        $cat->delete();
        $this->confirmingDeleteId = null;
        $this->dispatch('refreshCategories');
    }

    public function resetForm(): void
    {
        $this->categoryId = null;
        $this->parentId = null;
        $this->name = [];
        $this->slug = '';
        $this->manualSlug = false;
        $this->status = true;
    }

    public function render()
    {
        // Reload categories to reflect translation changes
        $this->loadCategories();
        return view('cms::livewire.blog-categories.index');
    }
}
