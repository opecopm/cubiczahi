@if($showModal && $modalType === 'step')
    <div class="modal modal-blur fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit' : 'Add' }} Step</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Step Name</label>
                        <input type="text" class="form-control" wire:model="stepName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Code</label>
                        <input type="text" class="form-control" wire:model="stepCode">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="isInitial" id="isInitial">
                                <label class="form-check-label" for="isInitial">Initial Step (Start)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="isFinal" id="isFinal">
                                <label class="form-check-label" for="isFinal">Final Step (End)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveStep">
                        {{ $updateMode ? 'Update' : 'Save' }} Step
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
