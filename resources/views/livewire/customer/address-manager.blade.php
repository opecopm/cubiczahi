<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: none;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.2rem;">&check;</span>
                <div>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 28px; flex-wrap: wrap;">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: #0a2463;">Saved Addresses</h3>
            <p class="text-muted mb-0">Manage delivery and pickup locations</p>
        </div>
        <button type="button" wire:click="create" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
            + Add Address
        </button>
    </div>

    @if($showForm)
        <form wire:submit="save" class="mb-4" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(10, 36, 99, 0.05)); border-radius: 12px; padding: 24px;">
            <h5 style="font-weight: 700; color: #0a2463; margin-bottom: 18px;">
                {{ $editingId ? 'Edit Address' : 'New Address' }}
            </h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address Type</label>
                    <select wire:model="address_type" class="form-control @error('address_type') is-invalid @enderror" style="border-radius: 8px; border: 2px solid #e5e7eb;">
                        <option value="delivery_address">Delivery Address</option>
                        <option value="pickup_address">Pickup Address</option>
                        <option value="billing_address">Billing Address</option>
                        <option value="shipping_address">Shipping Address</option>
                    </select>
                    @error('address_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <select wire:model="country" class="form-control @error('country') is-invalid @enderror" style="border-radius: 8px; border: 2px solid #e5e7eb;">
                        <option value="">Select country</option>
                        @foreach($countries as $countryOption)
                            <option value="{{ $countryOption->name }}">{{ $countryOption->name }}</option>
                        @endforeach
                    </select>
                    @error('country') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">State / Region</label>
                    <input type="text" wire:model="state" class="form-control @error('state') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb;" placeholder="Riyadh">
                    @error('state') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" wire:model="city" class="form-control @error('city') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb;" placeholder="Riyadh">
                    @error('city') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Address Line 1</label>
                    <input type="text" wire:model="line1" class="form-control @error('line1') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb;" placeholder="Street, building, area">
                    @error('line1') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Postal Code</label>
                    <input type="text" wire:model="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb;" placeholder="12345">
                    @error('postal_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Address Line 2</label>
                    <input type="text" wire:model="line2" class="form-control @error('line2') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb;" placeholder="Apartment, floor, notes">
                    @error('line2') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 pt-4 mt-4 border-top" style="border-color: #e5e7eb;">
                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                    <span wire:loading.remove>{{ $editingId ? 'Update Address' : 'Save Address' }}</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Saving...
                    </span>
                </button>
                <button type="button" wire:click="cancel" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                    Cancel
                </button>
            </div>
        </form>
    @endif

    @if($addresses->count())
        <div class="row g-3">
            @foreach($addresses as $addr)
                <div class="col-md-6" wire:key="address-{{ $addr->id }}">
                    <div class="card border-1 h-100" style="border-color: #e5e7eb; position: relative;" onmouseover="this.style.boxShadow='0 8px 16px rgba(13,110,253,0.15)'" onmouseout="this.style.boxShadow='none'">
                        <div class="card-body" style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px; margin-bottom: 12px;">
                                <div style="font-weight: 700; color: #0a2463; font-size: 1rem;">
                                    {{ ucwords(str_replace('_', ' ', $addr->address_type ?? 'saved_address')) }}
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button type="button" wire:click="edit({{ $addr->id }})" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px; padding: 4px 12px; font-size: 0.8rem;" title="Edit">Edit</button>
                                    <button type="button" wire:click="delete({{ $addr->id }})" wire:confirm="Delete this address?" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 12px; font-size: 0.8rem;" title="Delete">Delete</button>
                                </div>
                            </div>
                            <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                                <div style="color: #374151; line-height: 1.6; font-size: 0.95rem;">
                                    {{ $addr->line1 ?: '-' }}
                                    @if($addr->line2)
                                        <br>{{ $addr->line2 }}
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; gap: 12px; font-size: 0.85rem;">
                                <span class="text-muted">{{ $addr->city ?: '-' }}, {{ $addr->state ?: '-' }}</span>
                                <span class="text-muted">{{ $addr->postal_code ?: '-' }}</span>
                            </div>
                            <div class="text-muted mt-2" style="font-size: 0.85rem;">{{ $addr->country ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px;">
            <div style="font-size: 3rem; margin-bottom: 12px;">&#128205;</div>
            <p class="text-muted mb-3">No saved addresses yet</p>
            <button type="button" wire:click="create" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                Add Your First Address
            </button>
        </div>
    @endif
</div>
