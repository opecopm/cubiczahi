<?php

namespace Modules\CMS\Livewire\PageBuilder;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\PageBuilderPage;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;

class Index extends Component
{
    use WithPagination, WithModalTrait, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';
    public $page = [];
    public $pageId;
    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'page.title' => 'required|array',
            'page.slug' => 'required|string|max:255|unique:cms_pages,slug,' . $this->pageId,
            'page.meta_description' => 'nullable|array',
            'page.meta_keywords' => 'nullable|array',
            'page.status' => 'required|in:draft,published',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 100;
        $this->orderable = ['id', 'slug', 'status'];
    }

    public function resetInputFields()
    {
        $this->page = [];
        $this->pageId = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        PageBuilderPage::create(array_merge(
            $this->page,
            ['template_type' => 'page_builder']
        ));

        session()->flash('message', 'Page created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $page = PageBuilderPage::findOrFail($id);
        $this->pageId = $page->id;
        $this->page = $page->toArray();

        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $page = PageBuilderPage::findOrFail($this->pageId);
        $page->update($this->page);

        session()->flash('message', 'Page updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        PageBuilderPage::findOrFail($this->deleteId)->delete();
        $this->deleteId = "";
        session()->flash('message', 'Page deleted successfully.');
    }

    public function duplicate($id)
    {
        $page = PageBuilderPage::findOrFail($id);
        $newPage = $page->duplicate();

        session()->flash('message', 'Page duplicated successfully.');
    }

    public function render()
    {
        $pages = PageBuilderPage::query()
            ->where('template_type', 'page_builder')
            ->where(function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                      ->orWhere('slug', 'like', "%{$this->search}%")
                      ->orWhere('status', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('cms::livewire.page-builder.index', compact('pages'));
    }
}


