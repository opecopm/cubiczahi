<tr>
    <td class="text-secondary">{{ $company->id }}</td>
    <td>
        <div class="fw-bold">
            {!! str_repeat('<span class="text-secondary me-1">&mdash;</span>', $level) !!}
            {{ $company->name }}
        </div>
    </td>
    <td class="text-secondary">{{ $company->parent->name ?? 'N/A' }}</td>
    <td>
        <div class="btn-group">
            <button wire:click="edit({{ $company->id }})" class="btn btn-sm btn-ghost-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/></svg>
            </button>
            <button wire:click="confirmDelete({{ $company->id }})"
                    wire:confirm="Delete this company?"
                    class="btn btn-sm btn-ghost-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
            </button>
        </div>
    </td>
</tr>
