<?php

namespace Modules\IAM\Livewire\Teams;

use App\Livewire\WithModalTrait;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\IAM\Models\Team;
use Modules\IAM\Models\TeamMember;

class Show extends Component
{
    use WithModalTrait, WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $team;

    public $teamId;

    public $searchMember = '';

    // For Adding/Editing Member
    public $memberId; // team_member id

    public $userId; // user_id

    public $role = 'member';

    public $searchUser = '';

    public $searchResults = [];

    public $isEditing = false;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->team = Team::findOrFail($teamId);
    }

    public function updatedSearchUser()
    {
        $this->userId = null;

        if (strlen($this->searchUser) > 2) {
            $existingMemberIds = $this->team->members()->pluck('user_id')->toArray();

            $this->searchResults = User::where(function ($q) {
                $q->where('first_name', 'like', '%'.$this->searchUser.'%')
                    ->orWhere('last_name', 'like', '%'.$this->searchUser.'%')
                    ->orWhere('email', 'like', '%'.$this->searchUser.'%');
            })
                ->whereNotIn('id', $existingMemberIds)
                ->limit(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectUser($id)
    {
        $this->userId = $id;
        $user = User::find($id);
        if ($user) {
            $this->searchUser = $user->first_name.' '.$user->last_name.' ('.$user->email.')';
        }
        $this->searchResults = [];
    }

    public function addMember()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->openModal();
    }

    public function editMember($id)
    {
        $member = TeamMember::findOrFail($id);
        $this->memberId = $id;
        $this->userId = $member->user_id;
        $this->role = $member->role;
        $this->searchUser = $member->user->first_name.' '.$member->user->last_name.' ('.$member->user->email.')';
        $this->isEditing = true;
        $this->openModal();
    }

    public function storeMember()
    {
        $this->validate([
            'userId' => 'required|exists:users,id',
            'role' => 'required',
        ]);

        if ($this->isEditing) {
            $member = TeamMember::find($this->memberId);
            $member->update([
                'role' => $this->role,
            ]);
            session()->flash('message', 'Member updated successfully.');
        } else {
            TeamMember::create([
                'team_id' => $this->teamId,
                'user_id' => $this->userId,
                'role' => $this->role,
            ]);
            session()->flash('message', 'Member added successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        if ($this->deleteId) {
            TeamMember::find($this->deleteId)->delete();
            session()->flash('message', 'Member removed successfully.');
            $this->cancelDelete();
        }
    }

    public function resetInputFields()
    {
        $this->memberId = null;
        $this->userId = null;
        $this->role = 'member';
        $this->searchUser = '';
        $this->searchResults = [];
    }

    public function render()
    {
        $members = $this->team->members()
            ->with('user')
            ->whereHas('user', function ($q) {
                if ($this->searchMember) {
                    $q->where('first_name', 'like', '%'.$this->searchMember.'%')
                        ->orWhere('last_name', 'like', '%'.$this->searchMember.'%')
                        ->orWhere('email', 'like', '%'.$this->searchMember.'%');
                }
            })
            ->paginate(10);

        return view('iam::livewire.teams.show', compact('members'));
    }
}
