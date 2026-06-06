<?php

namespace Modules\IAM\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Business\Models\Location;
use Modules\System\Models\Menu;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use WithFileUploads;

    public $userId;

    public $user;

    public $first_name;

    public $last_name;

    public $email;

    public $password;

    public $role;

    public $menu_id;

    public $status;

    public $location_id;

    public $avatar;

    public function mount($id)
    {
        $this->userId = $id;
        $this->loadUser();
    }

    protected function loadUser(): void
    {
        $this->user = User::with(['roles'])->findOrFail($this->userId);

        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->menu_id = $this->user->menu_id;
        $this->status = $this->user->status;
        $this->location_id = $this->user->location_id;
        $this->role = $this->user->getRoleNames()->first() ?? '';
    }

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
            'location_id' => 'nullable|exists:locations,id',
            'avatar' => 'nullable|image|max:1024',
        ];
    }

    public function save()
    {
        $this->validate();

        $user = User::findOrFail($this->userId);
        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'menu_id' => $this->menu_id,
            'status' => $this->status,
            'location_id' => $this->location_id,
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
            $user->clearMediaCollection('avatars');
            $user->addMedia($this->avatar->getRealPath())->toMediaCollection('avatars');
        }

        $this->user = $user->fresh(['roles']);
        session()->flash('message', 'User updated successfully.');

        return redirect()->route('admin.iam.users.show', $user->id);
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        $menus = Menu::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('iam::livewire.users.edit', compact('roles', 'menus', 'locations'));
    }
}
