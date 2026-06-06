<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Edit Team Member</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="row g-3">

                <!-- Left Column -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Name (English)</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="translations.en.name" placeholder="Enter name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Designation (English)</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="translations.en.designation" placeholder="Enter designation">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio (English)</label>
                                <textarea class="form-control" rows="4"
                                    wire:model.defer="translations.en.bio" placeholder="Enter bio"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Message (English)</label>
                                <textarea class="form-control" rows="3"
                                    wire:model.defer="translations.en.message" placeholder="Enter message"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone (English)</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="translations.en.phone" placeholder="Enter phone number">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-footer">
                            <button type="button" wire:click="update" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-1"></i> Update Team Member
                            </button>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Photo</h3>
                        </div>
                        <div class="card-body">
                            <input type="file" class="form-control" wire:model="newPhoto">
                            @if ($newPhoto)
                                <img src="{{ $newPhoto->temporaryUrl() }}" class="img-fluid mt-2 rounded shadow" width="120">
                            @elseif ($photo)
                                <img src="{{ asset('storage/' . $photo) }}" class="img-fluid mt-2 rounded shadow" width="120">
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Social Media</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="url" class="form-control" wire:model="facebook"
                                    placeholder="Enter Facebook link">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="url" class="form-control" wire:model="twitter"
                                    placeholder="Enter Twitter link">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" wire:model="linkedin"
                                    placeholder="Enter LinkedIn link">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="url" class="form-control" wire:model="instagram"
                                    placeholder="Enter Instagram link">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="status" wire:model="status" value="1"
                                    @if($status) checked @endif>
                                <label class="form-check-label" for="status">Active</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" wire:model="sort_order" min="0"
                                    placeholder="Enter sort order">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
