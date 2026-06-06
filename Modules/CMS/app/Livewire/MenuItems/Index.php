<?php

namespace Modules\CMS\Livewire\MenuItems;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Modules\CMS\Models\MenuItem;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;

class Index extends Component
{
    use WithPagination, WithModalTrait, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    /** Translatable title array, keyed by locale code e.g. ['en'=>'Shop','ar'=>'تسوق'] */
    public array $title = [];
    public $url;
    public $order;
    public $parentId;
    public $icon;
    public $menuItemId;

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
            'url'      => 'nullable|string|max:500',
            'order'    => 'required|integer',
            'parentId' => 'nullable|exists:cms_menu_items,id',
            'icon'     => 'nullable|string|max:100',
        ];

        // Require at least the default locale title
        foreach ($this->activeLanguages() as $lang) {
            $required = $lang->is_default ? 'required' : 'nullable';
            $rules["title.{$lang->code}"] = "{$required}|string|max:255";
        }

        return $rules;
    }

    private function activeLanguages()
    {
        return \Modules\Global\Models\Language::where('status', 'active')->get();
    }

    public function mount()
    {
        $this->sortBy        = 'order';
        $this->sortDirection = 'asc';
        $this->perPage       = 500; // load all so drag covers everything
        $this->orderable     = ['id', 'order'];

        $languages          = $this->activeLanguages();
        $this->activeLocale = $languages->where('is_default', true)->first()?->code ?? 'en';

        // Initialise empty title for each active locale
        foreach ($languages as $lang) {
            $this->title[$lang->code] = '';
        }
    }

    /**
     * Called by SortableJS via Livewire.dispatch('updateMenuOrder', { orderedItems: [...] }).
     */
    #[On('updateMenuOrder')]
    public function updateOrder(array $orderedItems): void
    {
        foreach ($orderedItems as $item) {
            MenuItem::where('id', $item['id'])->update([
                'order'     => $item['order'],
                'parent_id' => $item['parentId'] ?? null,
            ]);
        }
        // Silent save — no flash message (WordPress-style)
    }

    public function resetInputFields(): void
    {
        foreach ($this->activeLanguages() as $lang) {
            $this->title[$lang->code] = '';
        }
        $this->url        = '';
        $this->order      = 0;
        $this->parentId   = null;
        $this->icon       = null;
        $this->menuItemId = '';
        $this->updateMode = false;
    }

    public function filter(): void {}

    public function store(): void
    {
        $this->validate();

        MenuItem::create([
            'title'     => $this->title,
            'url'       => $this->url,
            'order'     => $this->order,
            'parent_id' => $this->parentId ?: null,
            'icon'      => $this->icon,
        ]);

        session()->flash('message', 'MenuItem created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id): void
    {
        $languages  = $this->activeLanguages();
        $menuItem   = MenuItem::findOrFail($id);

        $this->menuItemId = $menuItem->id;
        $this->url        = $menuItem->url;
        $this->order      = $menuItem->order;
        $this->parentId   = $menuItem->parent_id;
        $this->icon       = $menuItem->icon;
        $this->updateMode = true;
        $this->showModal  = true;

        // Load each locale's title
        foreach ($languages as $lang) {
            $this->title[$lang->code] = $menuItem->getTranslation('title', $lang->code, false) ?? '';
        }
    }

    public function update(): void
    {
        $this->validate();

        $menuItem             = MenuItem::findOrFail($this->menuItemId);
        $menuItem->url        = $this->url;
        $menuItem->order      = $this->order;
        $menuItem->parent_id  = $this->parentId ?: null;
        $menuItem->icon       = $this->icon;

        // Save each locale
        foreach ($this->activeLanguages() as $lang) {
            $menuItem->setTranslation('title', $lang->code, $this->title[$lang->code] ?? '');
        }

        $menuItem->save();

        session()->flash('message', 'MenuItem updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete(): void
    {
        MenuItem::find($this->deleteId)?->delete();
        session()->flash('message', 'MenuItem deleted successfully.');
    }

    public function render()
    {
        $activeLanguages = $this->activeLanguages();

        $menuItems = MenuItem::where(function ($query) {
                $query->where('title->en', 'like', '%' . $this->search . '%')
                      ->orWhere('url', 'like', '%' . $this->search . '%');
            })
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('order', 'asc')->with(['children' => function ($q2) {
                    $q2->orderBy('order', 'asc');
                }]);
            }])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $allMenuItems = MenuItem::whereNull('parent_id')->orderBy('order')->get();

        return view('cms::livewire.menu-items.index', compact('menuItems', 'allMenuItems', 'activeLanguages'));
    }
}
