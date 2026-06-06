<div>
    {{-- Attribute groups ─────────────────────────────────────────────────── --}}
    @foreach ($availableAttributes as $attr)
        @php
            $attrRows = $groupedRows[$attr['id']] ?? [];
        @endphp

        <div class="card mb-3" wire:key="attr-group-{{ $attr['id'] }}">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold">{{ $attr['name'] }}</span>
                    @if ($attr['is_required'])
                        <span class="badge bg-red-lt">Required</span>
                    @else
                        <span class="badge bg-muted-lt">Optional</span>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary"
                        wire:click="addRow({{ $attr['id'] }})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                    Add option
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-vcenter card-table mb-0">
                    <thead>
                        <tr>
                            <th>Option name</th>
                            <th>Customer note</th>
                            <th style="width:120px">Price diff.</th>
                            <th class="text-center" style="width:80px">Default</th>
                            <th style="width:80px">Order</th>
                            <th style="width:100px">Status</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attrRows as $row)
                            @php $i = $row['_index']; @endphp
                            <tr wire:key="row-{{ $i }}">
                                {{-- hidden attribute_id --}}
                                <input type="hidden" wire:model="rows.{{ $i }}.attribute_id">

                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm @error("rows.{$i}.name") is-invalid @enderror"
                                           wire:model="rows.{{ $i }}.name"
                                           placeholder="e.g. Small">
                                    @error("rows.{$i}.name")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>

                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           wire:model="rows.{{ $i }}.note"
                                           placeholder="e.g. Up to 5 kg">
                                </td>

                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">±</span>
                                        <input type="number" step="0.01"
                                               class="form-control form-control-sm @error("rows.{$i}.price_difference") is-invalid @enderror"
                                               wire:model="rows.{{ $i }}.price_difference"
                                               placeholder="0.00">
                                    </div>
                                    @error("rows.{$i}.price_difference")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>

                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input"
                                           wire:model="rows.{{ $i }}.is_default">
                                </td>

                                <td>
                                    <input type="number" min="0"
                                           class="form-control form-control-sm"
                                           wire:model="rows.{{ $i }}.sort_order">
                                </td>

                                <td>
                                    <select class="form-select form-select-sm"
                                            wire:model="rows.{{ $i }}.status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </td>

                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-ghost-danger"
                                            wire:click="removeRow({{ $i }})"
                                            wire:confirm="Remove this option?">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-3">
                                    No options yet — click <strong>Add option</strong> above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    {{-- Rows with no attribute (newly added with blank attribute_id) ───────── --}}
    @if (!empty($groupedRows[''] ?? []) || !empty($groupedRows[0] ?? []))
        <div class="alert alert-warning">
            Some rows are missing an attribute selection. Please assign them or remove them.
        </div>
    @endif

    {{-- Actions ─────────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mt-2">
        <button type="button" class="btn btn-outline-secondary btn-sm"
                wire:click="addRow()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
            Add unassigned row
        </button>

        <button type="button" class="btn btn-primary" wire:click="save">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M14 4l0 4l-6 0l0 -4"/></svg>
            Save variants
        </button>
    </div>
</div>
