@php
    $rowNumber = isset($number) ? $number . '.' . $loop->iteration : (string) $loop->iteration;
@endphp
<tr>
    <td>{{ $rowNumber }}</td>
    <td>
        {!! str_repeat('&mdash; ', $level) !!}
        <a href="{{ route('accounting.accounts.show', ['account' => $account->id]) }}">{{ $account->code }}</a>
    </td>
    <td>{{ $account->name }}</td>
    <td>{{ \Modules\Accounting\Models\Account::TYPE_SELECT[$account->type] ?? $account->type }}</td>
    <td>{{ $account->currency }}</td>
    <td>
        @if ($account->parent)
            {{ $account->parent->code }} - {{ $account->parent->name }}
        @endif
    </td>
    <td>
        <span class="badge badge-sm bg-gradient-{{ $account->active ? 'success' : 'secondary' }}">
            {{ $account->active ? 'Yes' : 'No' }}
        </span>
    </td>
    <td class="align-middle">
        <a href="{{ route('accounting.accounts.edit', ['account' => $account->id]) }}" class="btn btn-sm btn-success btn-link">
            <i class="material-icons">edit</i>
        </a>
        <button wire:click="confirmDelete({{ $account->id }})" class="btn btn-sm btn-danger btn-link">
            <i class="material-icons">close</i>
        </button>
    </td>
</tr>

@if ($account->children && $account->children->count())
    @foreach ($account->children as $child)
        @include('accounting::livewire.accounts.partials.account-row', ['account' => $child, 'level' => $level + 1, 'number' => $rowNumber])
    @endforeach
@endif
