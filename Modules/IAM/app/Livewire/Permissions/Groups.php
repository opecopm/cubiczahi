<?php

namespace Modules\IAM\Livewire\Permissions;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\IAM\Models\PermissionGroup;

#[Layout('admin.layouts.app')]
class Groups extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteGroup(int $id): void
    {
        PermissionGroup::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Permission group deleted successfully.');
    }

    public function render()
    {
        $groups = PermissionGroup::query()
            ->withCount('permissions')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('iam::livewire.permissions.groups', ['groups' => $groups]);
    }
}
