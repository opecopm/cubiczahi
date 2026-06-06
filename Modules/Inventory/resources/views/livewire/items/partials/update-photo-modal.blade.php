<div class="modal fade @if ($photoModal) show d-block @endif" tabindex="-1" role="dialog"
    style="background: rgba(0, 0, 0, 0.5);"
    @if ($photoModal) aria-modal="true" @else aria-hidden="true" @endif>
    <div class="modal-dialog">
        <form wire:submit="updatePrimaryPhoto" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Primary Photo</h5>
                <button type="button" class="btn-close text-dark" wire:click="closePhotoModal">
                    <i class="fa fa-close"></i>
                </button>
            </div>

            <div class="modal-body">
                <input type="file" wire:model="primary_photo" class="form-control">
                @error('primary_photo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                @if ($primary_photo)
                    <div class="mt-3 text-center">
                        <p class="text-sm mb-2">Preview:</p>
                        <img src="{{ $primary_photo->temporaryUrl() }}" alt="Preview" class="img-fluid rounded shadow-sm">
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
