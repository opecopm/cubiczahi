@if($showEditModal)
<div class="modal modal-blur fade show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0,0,0,0.45); z-index: 1050;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px;">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" style="color: #1e293b; font-size: 1.1rem;">
                    @if($isCreationMode)
                        <i class="ti ti-plus me-1 text-primary"></i> Create New Banner
                    @else
                        <i class="ti ti-pencil me-1 text-primary"></i> Quick Edit Banner
                    @endif
                </h5>
                <button type="button" class="btn-close" wire:click="closeEditModal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="updateBanner">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Banner Name</label>
                        <input type="text" class="form-control @error('bannerName') is-invalid @enderror" 
                               wire:model.live="bannerName" placeholder="Enter banner name">
                        @error('bannerName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Slug</label>
                        <input type="text" class="form-control @error('bannerSlug') is-invalid @enderror" 
                               wire:model.defer="bannerSlug" placeholder="banner-slug">
                        @error('bannerSlug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Status</label>
                        <select class="form-select @error('bannerStatus') is-invalid @enderror" 
                                wire:model.defer="bannerStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('bannerStatus') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-link link-secondary fw-semibold text-decoration-none" wire:click="closeEditModal">Cancel</button>
                    <button type="submit" class="btn btn-primary ms-auto px-4 fw-bold">
                        @if($isCreationMode)
                            <i class="ti ti-plus me-1"></i> Create Banner
                        @else
                            <i class="ti ti-device-floppy me-1"></i> Save Changes
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
