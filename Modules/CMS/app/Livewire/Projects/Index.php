<?php

namespace Modules\CMS\Livewire\Projects;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Project;
use Illuminate\Support\Facades\App;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId;

    protected $updatesQueryString = ['search']; 

    public function updatingSearch()
    {
        $this->resetPage(); // reset pagination when search changes
    }

    public function render()
    {
        $locale = App::getLocale(); 

        $projects = Project::query()
            ->where("project_title->{$locale}", 'like', '%' . $this->search . '%')
            ->orWhere("short_description->{$locale}", 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('cms::livewire.projects.index', [
            'projects' => $projects
        ]);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Project::find($this->deleteId)?->delete();
            $this->deleteId = null;
            session()->flash('message', 'Project deleted successfully.');
        }
    }
}
