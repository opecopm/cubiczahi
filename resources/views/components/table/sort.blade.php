@props([
    'field',
    'orderable' => $orderable ?? [],
    'sortBy' => $sortBy ?? null,
    'sortDirection' => $sortDirection ?? null,
    'action' => 'sort',
])

@if (in_array($field, $orderable, true))
    @if ($sortBy !== $field)
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon icon-tabler ms-1"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            style="cursor: pointer;"
            wire:click="{{ $action }}('{{ $field }}')"
        >
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M3 9l4 -4l4 4" />
            <path d="M7 5v14" />
            <path d="M21 15l-4 4l-4 -4" />
            <path d="M17 19v-14" />
        </svg>
    @elseif ($sortDirection === 'desc')
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon icon-tabler ms-1 text-primary"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            style="cursor: pointer;"
            wire:click="{{ $action }}('{{ $field }}')"
        >
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M7 6v14" />
            <path d="M4 17l3 3l3 -3" />
            <path d="M17 4v14" />
            <path d="M14 7l3 -3l3 3" />
        </svg>
    @else
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon icon-tabler ms-1 text-primary"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            style="cursor: pointer;"
            wire:click="{{ $action }}('{{ $field }}')"
        >
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M7 4v14" />
            <path d="M4 7l3 -3l3 3" />
            <path d="M17 6v14" />
            <path d="M14 17l3 3l3 -3" />
        </svg>
    @endif
@endif

