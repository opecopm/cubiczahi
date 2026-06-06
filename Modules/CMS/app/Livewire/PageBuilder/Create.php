<?php

namespace Modules\CMS\Livewire\PageBuilder;

use Livewire\Component;
use Modules\CMS\Models\PageBuilderPage;
use Illuminate\Support\Str;

class Create extends Component
{
    public $page = [];

    protected function rules()
    {
        return [
            'page.slug' => 'required|string|max:255|unique:cms_pages,slug',
            'page.title.en' => 'required|string|min:1|max:255',
            'page.meta_description' => 'nullable|array',
            'page.meta_keywords' => 'nullable|array',
            'page.status' => 'required|in:draft,published',
        ];
    }

    public function mount()
    {
        $this->page = [
            'status' => 'draft',
            'template_type' => 'page_builder',
            'title' => [],
            'meta_description' => [],
            'meta_keywords' => []
        ];
    }

    public function generateSlug()
    {
        $title = $this->page['title']['en'] ?? null;
        if (!empty($title) && empty($this->page['slug'])) {
            $this->page['slug'] = Str::slug($title);
        }
    }

    public function save()
    {
        $this->validate();

        $page = PageBuilderPage::create($this->page);

        session()->flash('message', 'Page created successfully.');
        return redirect()->route('admin.cms.page-builder.builder', $page->id);
    }

    public function render()
    {
        return view('cms::livewire.page-builder.create');
    }
}


