<ul class="list-group list-group-flush mt-1 ms-4">
    @php($locale = app()->getLocale())
    @foreach($nodes as $node)
        <li class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $node->getTranslation('name', $locale) ?? $node->getTranslation('name', $defaultLocale) }}</strong>
                    <span class="badge bg-light text-dark ms-2">Slug: {{ $node->slug }}</span>
                    @if(!$node->status)
                        <span class="badge bg-warning text-dark ms-2">Inactive</span>
                    @endif
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" wire:click="openCreateModal({{ $node->id }})">Add Child</button>
                    <button class="btn btn-sm btn-outline-secondary" wire:click="openEditModal({{ $node->id }})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger"
                        wire:click="confirmDelete({{ $node->id }})"
                        wire:confirm="Are you sure you want to delete this category?">Delete</button>
                </div>
            </div>
            @if($node->children && $node->children->count())
                @include('cms::livewire.blog-categories.partials.tree', ['nodes' => $node->children])
            @endif
        </li>
    @endforeach
</ul>
