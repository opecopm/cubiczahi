<tr>
    <td class="text-secondary">{{ $company->id }}</td>
    <td>
        {!! str_repeat('<span class="text-secondary me-1">&mdash;</span>', $level) !!}
        <span class="fw-bold">{{ $company->getTranslation('name', 'en') }}</span>
    </td>
    <td class="text-secondary">{{ $company->getTranslation('name', $secondaryLang ?? 'ar') }}</td>
    <td class="text-secondary">{{ $company->code }}</td>
    <td class="text-secondary">{{ $company->crn }}</td>
    <td class="text-secondary">{{ $company->trn }}</td>
    <td>
        <span class="badge {{ $company->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
            {{ $company->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <button wire:click="edit({{ $company->id }})" class="btn btn-sm btn-ghost-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/></svg>
        </button>
    </td>
</tr>

@if ($company->children && $company->children->count())
    @foreach ($company->children as $childIndex => $child)
        @include('business::livewire.companies.company-row', ['company' => $child, 'level' => $level + 1])
    @endforeach
@endif
