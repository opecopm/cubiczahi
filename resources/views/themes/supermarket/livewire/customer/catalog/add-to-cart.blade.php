<div x-data="{ quickViewOpen: false }">
    @if(!$compact)
        @include('themes.supermarket.livewire.customer.catalog.add-to-cart-form')
    @else
        @if($item->activeVariants->count() > 0)
            <div class="d-flex w-100 gap-2">
                <a href="{{ lroute('catalog.show', $item->slug ?: $item->id) }}" wire:navigate class="btn btn-outline-primary fw-semibold text-nowrap flex-grow-1" style="border-radius:8px; font-size:0.9rem;">Options</a>
                <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center" style="border-radius: 8px; padding: 0.375rem 0.6rem;" @click="quickViewOpen = true" title="Quick View">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                      <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                    </svg>
                </button>
            </div>
        @else
            <div class="d-flex gap-2 flex-nowrap align-items-center w-100 justify-content-between">
                {{-- Quantity Selector --}}
                <div class="input-group d-none d-md-flex" style="width: 100px; border-radius: 8px; overflow:hidden; border: 1px solid #d1d5db;">
                    <button class="btn btn-light border-0 px-2" type="button" wire:click.prevent="decrement" style="font-weight:bold; color:#374151; background:#f9fafb;">&minus;</button>
                    <input type="text" class="form-control text-center border-0 bg-white shadow-none px-1" wire:model="quantity" readonly style="font-weight:600; color:#1f2937; font-size:0.9rem;">
                    <button class="btn btn-light border-0 px-2" type="button" wire:click.prevent="increment" style="font-weight:bold; color:#374151; background:#f9fafb;">&#43;</button>
                </div>

                {{-- Add to Cart Button --}}
                <button type="button" wire:click.prevent="addToCart" class="btn btn-primary fw-semibold flex-grow-1 text-nowrap" style="border-radius: 8px; display:flex; align-items:center; justify-content:center; gap:4px; font-size: 0.85rem; padding: 0.375rem 0.5rem;">
                    <span wire:loading.remove wire:target="addToCart"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16"><path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/><path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg></span>
                    <span wire:loading wire:target="addToCart" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Cart</span>
                </button>

                {{-- Quick View Button --}}
                <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center" style="border-radius: 8px; display:flex; align-items:center; justify-content:center; gap:4px; font-size: 0.85rem; padding: 0.500rem 0.5rem;" @click="quickViewOpen = true" title="Quick View">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                      <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Alpine-powered Modal (Bypasses Bootstrap JS errors) --}}
        <template x-teleport="body">
            <div x-show="quickViewOpen" x-transition.opacity style="display: none;">
                <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5); z-index: 1055;" @click.self="quickViewOpen = false">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden position-relative" style="white-space: normal;">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3" @click="quickViewOpen = false" aria-label="Close" style="background-color: white; border-radius: 50%; padding: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></button>
                            <div class="row g-0">
                                <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4">
                                    @if($item->primaryImage)
                                        <img src="{{ $item->primaryImage->url }}" alt="{{ $item->getTranslation('name','en') }}" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;">
                                    @else
                                        <div class="text-muted" style="font-size: 5rem;">&#128230;</div>
                                    @endif
                                </div>
                                <div class="col-md-7 text-start">
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
                                            @include('themes.supermarket.livewire.customer.catalog.add-to-cart-form', ['modalId' => 'modal'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
