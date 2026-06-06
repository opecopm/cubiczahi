<?php

namespace Modules\CMS\Livewire\PageBuilder;

use Livewire\Component;
use Modules\CMS\Models\PageBuilderPage;
use Modules\CMS\Models\Page;

class Show extends Component
{
    public $page;

    public function mount($id)
    {
        // Try to find as PageBuilderPage first, then as regular Page
        $this->page = PageBuilderPage::find($id);
        if (!$this->page) {
            $this->page = Page::findOrFail($id);
        }
    }

    public function render()
    {
        return view('cms::livewire.page-builder.show');
    }
}
