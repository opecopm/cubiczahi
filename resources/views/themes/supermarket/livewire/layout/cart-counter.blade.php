<a href="{{ lroute('cart.index') }}" wire:navigate class="btn position-relative d-flex align-items-center justify-content-center" style="width:42px; height:42px; border-radius:50%; border: 1px solid #e5e7eb; color:#4b5563; background:#fff; text-decoration:none;">
    &#128722;
    @if($count > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="font-size:0.65rem; background:#ef4444; color:#fff;">
            {{ $count }}
        </span>
    @endif
</a>
