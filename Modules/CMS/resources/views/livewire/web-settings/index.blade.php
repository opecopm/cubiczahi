<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Website Settings</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            {{-- Language Switcher --}}
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Language</label>
                                <select class="form-control form-select" wire:model.live="locale">
                                    @foreach ($activeLanguages as $lang)
                                        <option value="{{ $lang->code }}">
                                            🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Translatable Fields --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Site Name ({{ strtoupper($locale) }})</label>
                                <input type="text" class="form-control" wire:model.defer="site_name.{{ $locale }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Footer About ({{ strtoupper($locale) }})</label>
                                <textarea class="form-control" rows="3" wire:model.defer="footer_about.{{ $locale }}"></textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Footer Text ({{ strtoupper($locale) }})</label>
                                <textarea class="form-control" rows="2" wire:model.defer="footer_text.{{ $locale }}"></textarea>
                            </div>

                            {{-- Non-translatable Fields --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" wire:model.defer="contact_email">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" wire:model.defer="contact_phone">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Contact Address</label>
                                <textarea class="form-control" wire:model.defer="contact_address"></textarea>
                            </div>

                            {{-- Social Links --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Whatsapp Link [Example: https://wa.me/96600000000]</label>
                                <input type="text" class="form-control" wire:model.defer="social_whatsapp">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="text" class="form-control" wire:model.defer="social_facebook">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="text" class="form-control" wire:model.defer="social_twitter">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="text" class="form-control" wire:model.defer="social_instagram">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">LinkedIn</label>
                                <input type="text" class="form-control" wire:model.defer="social_linkedin">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Google Maps Location (Name or Embed URL)</label>
                                <textarea class="form-control" rows="3" wire:model.defer="map_iframe"></textarea>
                                <small class="text-muted">Enter a location name (e.g., "Jeddah, Saudi Arabia") OR paste the Google Maps 'src' URL.</small>
                            </div>

                            {{-- Header Logo --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Header Logo</label>
                                <input type="file" class="form-control" wire:model="header_logo">

                                @if($header_logo && !is_string($header_logo))
                                    <div class="mt-2 position-relative d-inline-block">
                                        <img src="{{ $header_logo->temporaryUrl() }}" class="img-thumbnail" width="150">
                                        <button type="button" wire:click="$set('header_logo', null)" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; line-height: 24px; transform: translate(50%, -50%);">&times;</button>
                                    </div>
                                @else
                                    @php
                                        $existingHeader = \Modules\CMS\Models\WebSetting::where('key', 'header_logo')->first()?->getFirstMediaUrl('header_logo');
                                    @endphp
                                    @if($existingHeader)
                                        <div class="mt-2 position-relative d-inline-block">
                                            <img src="{{ $existingHeader }}" class="img-thumbnail" width="150" style="background: #ccc;">
                                            <button type="button" wire:confirm="Are you sure you want to remove the header logo?" wire:click="deleteHeaderLogo" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; line-height: 24px; transform: translate(50%, -50%);">&times;</button>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Footer Logo --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Footer Logo</label>
                                <input type="file" class="form-control" wire:model="footer_logo">

                                @if($footer_logo && !is_string($footer_logo))
                                   <div class="mt-2 position-relative d-inline-block">
                                        <img src="{{ $footer_logo->temporaryUrl() }}" class="img-thumbnail" width="150">
                                        <button type="button" wire:click="$set('footer_logo', null)" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; line-height: 24px; transform: translate(50%, -50%);">&times;</button>
                                    </div>
                                @else
                                   @php
                                        $existingFooter = \Modules\CMS\Models\WebSetting::where('key', 'footer_logo')->first()?->getFirstMediaUrl('footer_logo');
                                    @endphp
                                    @if($existingFooter)
                                        <div class="mt-2 position-relative d-inline-block">
                                            <img src="{{ $existingFooter }}" class="img-thumbnail" width="150" style="background: #ccc;">
                                            <button type="button" wire:confirm="Are you sure you want to remove the footer logo?" wire:click="deleteFooterLogo" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; line-height: 24px; transform: translate(50%, -50%);">&times;</button>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Site Favicon --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Favicon</label>
                                <input type="file" class="form-control" wire:model="site_favicon">

                                @if($site_favicon && !is_string($site_favicon))
                                   <div class="mt-2 position-relative d-inline-block">
                                        <img src="{{ $site_favicon->temporaryUrl() }}" class="img-thumbnail" width="60">
                                        <button type="button" wire:click="$set('site_favicon', null)" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; line-height: 24px; transform: translate(50%, -50%);">&times;</button>
                                    </div>
                                @else
                                   @php
                                        $existingFavicon = \Modules\CMS\Models\WebSetting::where('key', 'site_favicon')->first()?->getFirstMediaUrl('site_favicon');
                                    @endphp
                                    @if($existingFavicon)
                                        <div class="mt-2 position-relative d-inline-block">
                                            <img src="{{ $existingFavicon }}" class="img-thumbnail" width="60" style="background: #ccc;">
                                            <button type="button" wire:confirm="Are you sure you want to remove the site favicon?" wire:click="deleteSiteFavicon" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; line-height: 24px; transform: translate(50%, -50%);">&times;</button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit"
                                class="btn btn-primary"
                                wire:loading.attr="disabled"
                                wire:target="save">
                                <i class="ti ti-device-floppy me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
