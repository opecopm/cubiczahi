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

    @if(!$isEditing)
        {{-- View Mode --}}
        <div style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(10, 36, 99, 0.05)); border-radius: 12px; padding: 24px; margin-bottom: 28px;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</label>
                    <div style="font-size: 1.1rem; font-weight: 600; color: #0a2463;">{{ $name ?: '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">Email</label>
                    <div style="font-size: 1.1rem; font-weight: 600; color: #0a2463;">{{ $email ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">Member Since</label>
                    <div style="font-size: 1.1rem; font-weight: 600; color: #0a2463;">{{ auth()->user()->created_at?->format('M d, Y') ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">Phone</label>
                    <div style="font-size: 1.1rem; font-weight: 600; color: #0a2463;">
                        @if($phone)
                            {{ $phone_code ? '+' . $phone_code . ' ' : '' }}{{ $phone }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <button wire:click="toggleEdit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
            Edit Profile
        </button>
    @else
        {{-- Edit Mode --}}
        <form wire:submit="save" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(10, 36, 99, 0.05)); border-radius: 12px; padding: 28px;">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Full Name</label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px; font-size: 0.95rem;" placeholder="Your full name">
                    @error('name')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Email</label>
                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
                           style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px; font-size: 0.95rem;" placeholder="your@email.com">
                    @error('email')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-4">
                    <div class="row g-2">
                        <div class="col-5">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Code</label>
                            <select wire:model="phone_code" class="form-control @error('phone_code') is-invalid @enderror"
                                    style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px; font-size: 0.95rem;">
                                <option value="">Select</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->phone_code }}">
                                        {{ $country->iso2 }} +{{ $country->phone_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('phone_code')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-7">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Phone</label>
                            <input type="tel" wire:model="phone" class="form-control @error('phone') is-invalid @enderror"
                                   style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px; font-size: 0.95rem;" placeholder="501234567">
                            @error('phone')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 pt-4 border-top" style="border-color: #e5e7eb;">
                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Saving...
                    </span>
                </button>
                <button type="button" wire:click="toggleEdit" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                    Cancel
                </button>
            </div>
        </form>
    @endif
</div>
