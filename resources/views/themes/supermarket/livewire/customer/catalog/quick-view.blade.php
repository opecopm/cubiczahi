<div x-data="{ open: @entangle('isOpen') }">
    <div x-show="open" class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5); z-index: 1055;" @click.self="open = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3" @click="open = false" aria-label="Close" style="background-color: white; border-radius: 50%; padding: 0.5rem;"></button>
                @if($item)
                    <div class="row g-0">
                        <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4">
                            @if($item->primaryImage)
                                <img src="{{ $item->primaryImage->url }}" alt="{{ $item->getTranslation('name','en') }}" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;">
                            @else
                                <div class="text-muted" style="font-size: 5rem;">&#128230;</div>
                            @endif
                        </div>
                        <div class="col-md-7">
                            <div class="p-4 p-md-5 d-flex flex-column h-100">
                                <h3 class="fw-bold mb-2" style="color: #1e293b;">{{ $item->getTranslation('name','en') }}</h3>
                                @if($item->category)
                                    <div class="mb-3">
                                        <span class="badge bg-secondary">{{ $item->category->name }}</span>
                                    </div>
                                @endif
                                
                                @if($item->short_description)
                                    <p class="text-muted mb-4" style="font-size: 0.95rem;">{!! \Illuminate\Support\Str::limit(strip_tags($item->short_description), 150) !!}</p>
                                @endif
                                
                                <div class="mt-auto">
                                    <livewire:customer.catalog.add-to-cart :item="$item" :wire:key="'quick-view-'.$item->id" />
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-5 text-center">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
