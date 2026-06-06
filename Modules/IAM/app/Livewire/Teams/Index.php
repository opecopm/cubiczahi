<?php

namespace Modules\IAM\Livewire\Teams;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\IAM\Models\Team;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $name;

    public $description;

    public $teamId;

    public $updateMode = false;

    public int $perPage;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'description' => 'nullable',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->orderable = ['id', 'name'];
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->teamId = '';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        Team::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Team Created Successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        $this->teamId = $id;
        $this->name = $team->name;
        $this->description = $team->description;
        $this->updateMode = true;
        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        if ($this->teamId) {
            $team = Team::find($this->teamId);
            $team->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('message', 'Team Updated Successfully.');
            $this->resetInputFields();
            $this->closeModal();
        }
    }

    public function delete()
    {
        if ($this->deleteId) {
            Team::find($this->deleteId)->delete();
            session()->flash('message', 'Team Deleted Successfully.');
            $this->cancelDelete();
        }
    }

    public function render()
    {
        $query = Team::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('description', 'like', '%'.$this->search.'%');
        }

        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $teams = $query->paginate($this->perPage);

        return view('iam::livewire.teams.index', compact('teams'));
    }
}
