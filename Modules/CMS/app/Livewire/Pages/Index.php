<?php

namespace Modules\CMS\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Page;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;

class Index extends Component
{
    use WithPagination, WithModalTrait, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';
    public $statusFilter = '';
    public $page = [];
    public $pageId;
    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'page.title' => 'required|string|min:3|max:255',
            'page.slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $this->pageId,
            'page.content' => 'nullable|string',
            'page.status' => 'required|in:draft,published',
            'page.template_type' => 'nullable|in:default,custom,page_builder',
            'page.template_name' => 'nullable|string|max:255',
            'page.parent_id' => 'nullable|integer|exists:cms_pages,id',
            'page.published_at' => 'nullable|date',
            'page.meta_description' => 'nullable|string|max:255',
            'page.meta_keywords' => 'nullable|string|max:255',
            'page.canonical_url' => 'nullable|url|max:255',
            'page.og_title' => 'nullable|string|max:255',
            'page.og_description' => 'nullable|string|max:1000',
            'page.og_url' => 'nullable|url|max:255',
            'page.og_type' => 'nullable|string|max:50',
            'page.og_site_name' => 'nullable|string|max:255',
            'page.og_locale' => 'nullable|string|max:20',
            'page.published_time' => 'nullable|date',
            'page.modified_time' => 'nullable|date',
            'page.twitter_card' => 'nullable|string|max:50',
            'page.twitter_title' => 'nullable|string|max:255',
            'page.twitter_description' => 'nullable|string|max:1000',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 100;
        $this->orderable = ['id', 'title', 'slug', 'status'];
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
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

        Page::create(array_merge(
            $this->page,
        ));

        session()->flash('message', 'Page created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $this->pageId = $page->id;
        $this->page = $page->toArray();

        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $page = Page::findOrFail($this->pageId);
        $page->update(array_merge(
            $this->page,
        ));

        session()->flash('message', 'Page updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        Page::findOrFail($this->deleteId)->delete();
        $this->deleteId = "";
        session()->flash('message', 'Page deleted successfully.');
    }

    public function render()
    {
        $query = Page::query()
            ->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            });

        $totalCount     = (clone $query)->count();
        $publishedCount = (clone $query)->where('status', 'published')->count();
        $draftCount     = (clone $query)->where('status', 'draft')->count();

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $pages = $query->orderBy($this->sortBy, $this->sortDirection)
                       ->paginate($this->perPage);

        return view('cms::livewire.pages.index', compact(
            'pages', 'totalCount', 'publishedCount', 'draftCount'
        ));
    }
}
