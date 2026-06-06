<?php

namespace Modules\CMS\Livewire\Blogs;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Blog;
use Illuminate\Support\Facades\App;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId;

    protected $updatesQueryString = ['search']; // keeps search in URL

    public function updatingSearch()
    {
        $this->resetPage(); // reset pagination when search changes
    }

    public function render()
    {
        $locale = App::getLocale(); // current locale

        // 🔹 Since Spatie stores translations in JSON, we query JSON fields directly
        $blogs = Blog::where("title->{$locale}", 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('cms::livewire.blogs.index', [
            'blogs' => $blogs
        ]);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Blog::find($this->deleteId)?->delete();
            $this->deleteId = null;
            session()->flash('message', 'Blog deleted successfully.');
        }
    }
}
