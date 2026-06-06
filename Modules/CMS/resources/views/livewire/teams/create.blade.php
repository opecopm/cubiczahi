<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Add Team Member</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <form wire:submit.prevent="save">
                <div class="row g-3">

                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control"
                                        wire:model="translations.en.name" placeholder="Enter name">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-control"
                                        wire:model="translations.en.designation" placeholder="Enter designation">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea class="form-control" rows="4"
                                        wire:model="translations.en.bio" placeholder="Enter bio"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="3"
                                        wire:model="translations.en.message" placeholder="Enter message"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control"
                                        wire:model="translations.en.phone" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-device-floppy me-1"></i> Save Team Member
                                </button>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Photo</h3>
                            </div>
                            <div class="card-body">
                                <input type="file" class="form-control" wire:model="photo">
                                @if ($photo)
                                    <div class="mt-3">
                                        <img src="{{ $photo->temporaryUrl() }}"
                                            class="img-fluid rounded border"
                                            style="max-height: 180px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Contact & Social</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control"
                                        wire:model="email" placeholder="Enter email">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Facebook</label>
                                    <input type="url" class="form-control"
                                        wire:model="facebook" placeholder="Enter Facebook link">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Twitter</label>
                                    <input type="url" class="form-control"
                                        wire:model="twitter" placeholder="Enter Twitter link">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" class="form-control"
                                        wire:model="linkedin" placeholder="Enter LinkedIn link">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Instagram</label>
                                    <input type="url" class="form-control"
                                        wire:model="instagram" placeholder="Enter Instagram link">
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="status" wire:model="status" value="1">
                                    <label class="form-check-label" for="status">Active</label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" wire:model="sort_order" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
