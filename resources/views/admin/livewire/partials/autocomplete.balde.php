<!-- resources/views/livewire/search-component.blade.php -->
<div>
    <input
        type="text"
        wire:model="autoCompleteKeyword"
        placeholder="Search..."
        class="form-control"
    >

    <ul class="list-group mt-2">
        @foreach($suggestions as $suggestion)
            <li class="list-group-item">
                {{ $suggestion->name }} <!-- Adjust based on your model's attribute -->
            </li>
        @endforeach
    </ul>
</div>
