<div class="position-relative" x-data="{ open: false }">
    <label class="form-label d-flex align-items-center justify-content-between mb-1">
        <span>
            {{ $label }}
            @if(!empty($addNewUrl))
                (<a href="{{ $addNewUrl }}" target="_blank" class="text-primary">Add New</a>)
            @endif
        </span>
        <span class="d-inline-flex align-items-center gap-2">
            @if(!empty($value))
                <a href="#" wire:click.prevent="clearSelection" class="text-secondary d-inline-flex align-items-center" title="Clear">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </a>
            @endif
            <a href="#" wire:click.prevent="refreshOptions" class="text-secondary d-inline-flex align-items-center" title="Refresh">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/></svg>
            </a>
        </span>
    </label>
    <div class="input-group">
        @if(!empty($icon))
            <span class="input-group-text">
                <i class="ti ti-{{ $icon }}"></i>
            </span>
        @endif
        <input
            type="text"
            class="form-control"
            wire:model.live.debounce.300ms="search"
            wire:focus="refreshOptions"
            wire:click="refreshOptions"
            @focus="open = true"
            @input="open = true"
            @click.outside="open = false"
        >
    </div>

    @if(!empty($options) && empty($value))
        <ul class="list-group position-absolute w-100 shadow bg-white start-0"
            style="z-index: 1050; max-height: 200px; overflow-y: auto; overflow-x: hidden; top: 100%;"
            x-show="open"
            x-transition
        >
            @foreach($options as $opt)
                <li class="list-group-item list-group-item-action cursor-pointer border-0"
                    wire:click="selectOption(@js($opt['id']))"
                    @click="open = false"
                    wire:key="entity-option-{{ $entity }}-{{ $opt['id'] }}"
                    style="cursor: pointer;"
                >
                    <div class="d-flex flex-column">
                        <span class="text-sm font-weight-bold">{{ $opt['primary'] }}</span>
                        @if(!empty($opt['secondary']))
                            <small class="text-xs text-muted">{{ $opt['secondary'] }}</small>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
