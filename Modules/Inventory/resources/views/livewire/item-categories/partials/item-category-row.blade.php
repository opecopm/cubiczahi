<tr>
    <td class="text-secondary">{{ $category->id }}</td>
    <td>{{ $category->code }}</td>
    <td>
        {!! str_repeat('&mdash; ', $level) !!} {{ $category->name }}
    </td>
    <td>{{ $category->parent->name ?? 'N/A' }}</td>
    <td class="w-1">
        <div class="dropdown">
            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
            <div class="dropdown-menu dropdown-menu-end">
                @can('update_item_categories')
                    <button wire:click="edit({{ $category->id }})" class="dropdown-item">Edit</button>
                @endcan
                @can('delete_item_categories')
                    <button wire:click="confirmDelete({{ $category->id }})" wire:confirm="Are you sure you want to delete this category?" class="dropdown-item text-danger">Delete</button>
                @endcan
            </div>
        </div>
    </td>
</tr>

@if ($category->children && $category->children->count())
    @foreach ($category->children as $child)
        @include('inventory::livewire.item-categories.partials.item-category-row', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
