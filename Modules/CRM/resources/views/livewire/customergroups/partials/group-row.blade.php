@php
    $indent = str_repeat('&mdash;', $level);
@endphp
<tr>
    <td>{{ $group->id }}</td>
    <td>{!! $indent !!}{{ $group->name }}</td>
    <td>{{ optional($group->parent)->name ?? '-' }}</td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
            <div class="dropdown-menu dropdown-menu-end">
                <button type="button" class="dropdown-item" wire:click="edit({{ $group->id }})">Edit</button>
                <button type="button" class="dropdown-item text-danger" wire:click="confirmDelete({{ $group->id }})">Delete</button>
            </div>
        </div>
    </td>
</tr>
@if($group->children && $group->children->count())
    @foreach($group->children as $child)
        @include('crm::livewire.customergroups.partials.group-row', ['group' => $child, 'level' => $level + 1])
    @endforeach
@endif
