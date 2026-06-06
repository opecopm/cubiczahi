<?php

namespace Modules\IAM\Livewire\Users;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\System\Models\Menu;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithFileUploads, WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $first_name;

    public $last_name;

    public $email;

    public $password;

    public $role;

    public $menu_id;

    public $status;

    public $avatar;

    public $userId;

    public $updateMode = false;

    public $model;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
        'filters' => ['except' => []],
    ];

    public $mediaComponentNames = ['avatar'];

    protected function rules()
    {
        return [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$this->userId,
            'password' => 'nullable|min:6',
            'role' => 'required',
            'menu_id' => 'required',
            'status' => 'nullable',
            'avatar' => 'nullable|image|max:1024', // 1MB Max
        ];
    }

    public function mount()
    {
        $this->role = optional(auth()->user()->roles->first())->name ?? '';

        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 10; // Default pagination limit
        $this->orderable = ['id', 'first_name', 'last_name', 'email'];

        $this->model = new User;
        $this->initFilters($this->model);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->userId = '';
        $this->status = '';
        $this->updateMode = false;
        $this->avatar = null;
    }

    public function store()
    {
        $this->validate();
        if ($this->password) {
            $user = User::create([
                'type' => 'backend',
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'menu_id' => $this->menu_id,
                'status' => $this->status,
            ]);

            if ($this->role) {
                $user->syncRoles($this->role);
            }

            if ($this->avatar) {
                $user->clearMediaCollection('avatars'); // Clear existing media
                $user->addMedia($this->avatar->getRealPath())->toMediaCollection('avatars');
            }

        }
        session()->flash('message', 'User created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {

        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->menu_id = $user->menu_id;
        $this->status = $user->status;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $user = User::find($this->userId);
        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'menu_id' => $this->menu_id,
            'status' => $this->status,
        ]);
        if ($this->password) {
            $user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        if ($this->role) {
            $user->syncRoles($this->role);
        }

        if ($this->avatar) {
            $user->clearMediaCollection('avatars'); // Clear existing media
            $media = $user->addMedia($this->avatar->getRealPath())->toMediaCollection('avatars');
        }

        session()->flash('message', 'User updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        $user = User::find($this->deleteId);
        if (!$user) {
            session()->flash('error', 'User not found.');
            $this->closeModal();
            return;
        }

        // Prevent self-deletion
        if (auth()->id() === $user->id) {
            session()->flash('error', 'You cannot delete your own user account.');
            return;
        }

        // Prevent deletion if user is the only admin
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        if ($adminRole && $user->hasRole('admin')) {
            $adminCount = User::role('admin')->count();
            if ($adminCount <= 1) {
                session()->flash('error', 'Cannot delete the last admin user. Assign admin role to another user first.');
                return;
            }
        }

        // Detach from pivot tables
        $user->companies()->detach();
        $user->locations()->detach();

        // Delete related media (if using spatie media library)
        $user->clearMediaCollection('avatars');

        // Delete the user
        $user->delete();

        session()->flash('message', 'User deleted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $roles = Role::all();
        $menus = Menu::all();

        $usersCount = User::count();
        $activeUsersCount = User::where('status', 'active')->count();
        $inactiveUsersCount = User::where('status', 'inactive')->count();
        $otherUsersCount = User::whereNotIn('status', ['active', 'inactive'])->count();

        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%'.$this->search.'%')
                    ->orWhere('last_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if (isset($this->filters['status']) && $this->filters['status'] === 'other') {
            $query->whereNotIn('status', ['active', 'inactive']);
            $currentStatus = $this->filters['status'];
            unset($this->filters['status']);
            $query = $this->applyFilters($query, $this->model);
            $this->filters['status'] = $currentStatus;
        } else {
            $query = $this->applyFilters($query, $this->model);
        }

        $users = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('iam::livewire.users.index', compact('users', 'roles', 'menus', 'usersCount', 'activeUsersCount', 'inactiveUsersCount', 'otherUsersCount'));
    }
}
