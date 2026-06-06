<?php

namespace Modules\CMS\Livewire\Forms;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Form;
use Illuminate\Support\Facades\App;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $locale = App::getLocale();

        $forms = Form::where("title->{$locale}", 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('cms::livewire.forms.index', [
            'forms' => $forms
        ]);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            Form::find($this->deleteId)?->delete();
            $this->deleteId = null;
            session()->flash('message', 'Form deleted successfully.');
        }
    }
}
