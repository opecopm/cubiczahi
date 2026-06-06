@if($type === 'spare_part')
    <div class="modal fade @if($showBulkAllocationModal) show d-block @endif" role="dialog" style="background: rgba(0, 0, 0, 0.5);" @if($showBulkAllocationModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Bulk Allocation
                        @if($bulkItem)
                            - {{ $bulkItem->reference }} {{ $bulkItem->getTranslation('name','en') }}
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeBulkAllocationModal"></button>
                </div>
                <div class="modal-body" style="min-height: 500px;">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <livewire:entity-select
                                :key="'spare-bulk-source-location-'.$bulk_item_id.'-'.$showBulkAllocationModal"
                                entity="location"
                                label="Source Location (Warehouse)"
                                wire:model.live="bulk_source_location_id"
                            />
                            @error('bulk_source_location_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    @error('bulk_user_rows')<div class="text-danger mt-2">{{ $message }}</div>@enderror

                    <div class="table-responsive mt-3">
                        <table class="table table-sm mb-0" style="min-height: 300px;">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th class="text-end" style="width: 180px;">Qty</th>
                                    <th class="text-end" style="width: 160px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bulk_user_rows as $i => $row)
                                    <tr>
                                        <td>
                                            <livewire:entity-select
                                                :key="'spare-bulk-user-'.$i.'-'.$bulk_source_location_id.'-'.$bulk_item_id.'-'.$showBulkAllocationModal"
                                                entity="user"
                                                label="User"
                                                wire:model.live="bulk_user_rows.{{ $i }}.user_id"
                                                :params="[
                                                    'location_id' => (int) ($bulk_source_location_id ?? 0),
                                                ]"
                                            />
                                            @error("bulk_user_rows.$i.user_id")<span class="text-danger">{{ $message }}</span>@enderror
                                        </td>
                                        <td class="text-end">
                                            <label>&nbsp;</label>
                                            <input type="number" step="0.0001" class="form-control border border-1 px-2 text-end" wire:model="bulk_user_rows.{{ $i }}.requested_quantity">
                                            @error("bulk_user_rows.$i.requested_quantity")<span class="text-danger">{{ $message }}</span>@enderror
                                        </td>
                                        <td class="text-end">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-sm btn-outline-dark" wire:click="addBulkUserRow">Add</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeBulkUserRow({{ $i }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeBulkAllocationModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="storeBulkAllocation" @if(!$bulk_source_location_id || !$bulk_item_id) disabled @endif>Submit</button>
                </div>
            </div>
        </div>
    </div>
@endif
