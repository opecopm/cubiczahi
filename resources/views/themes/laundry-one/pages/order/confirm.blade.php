@extends('themes.laundry-one.layouts.guest')

@section('content')
<livewire:customer.layout.navigation />

<div style="background:#f0f4f8; min-height:80vh; padding:60px 0 90px;">
<div class="container" style="max-width:640px;">
    @include('themes.laundry-one.partials.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard', 'url' => route('customer.dashboard')],
            ['label' => 'Orders', 'url' => route('customer.orders.index')],
            ['label' => 'Order Confirmed'],
        ],
    ])

    <div class="bg-white rounded-4 shadow-sm overflow-hidden">

        {{-- Success header --}}
        <div style="background:linear-gradient(135deg,#0a2463,#0d6efd); padding:40px 32px; text-align:center;">
            <div style="width:72px;height:72px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1rem;">
                &#10003;
            </div>
            <h2 style="color:#fff;font-weight:800;margin:0 0 0.4rem;">Order Confirmed!</h2>
            <div style="color:rgba(255,255,255,0.8);font-size:0.95rem;">
                Reference: <strong style="color:#fff;">{{ $order->reference ?? '#'.$order->id }}</strong>
            </div>
        </div>

        {{-- Order items --}}
        <div style="padding:28px 32px;">
            <h6 style="font-weight:700;color:#374151;margin-bottom:1rem;font-size:0.82rem;letter-spacing:1px;text-transform:uppercase;">
                Order Summary
            </h6>

            @foreach($order->items as $orderItem)
                <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:10px 0;border-bottom:1px solid #f1f3f5;">
                    <div>
                        <div style="font-weight:600;color:#0a2463;font-size:0.95rem;">{{ $orderItem->name }}</div>
                        @if($orderItem->description)
                            <div style="font-size:0.78rem;color:#9ca3af;margin-top:2px;">+ {{ $orderItem->description }}</div>
                        @endif
                        <div style="font-size:0.8rem;color:#6c757d;margin-top:2px;">Qty: {{ $orderItem->quantity }}</div>
                    </div>
                    <div style="font-weight:700;color:#0a2463;white-space:nowrap;">
                        {{ number_format($orderItem->total, 2) }} SAR
                    </div>
                </div>
            @endforeach

            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:16px;margin-top:4px;">
                <span style="font-weight:700;color:#374151;">Total</span>
                <span style="font-size:1.3rem;font-weight:800;color:#0d6efd;">
                    {{ number_format($order->total, 2) }} SAR
                </span>
            </div>

            {{-- Status --}}
            <div style="background:#f0f4f8;border-radius:12px;padding:14px 18px;margin-top:20px;display:flex;align-items:center;gap:12px;">
                <div style="font-size:1.5rem;">&#128666;</div>
                <div>
                    <div style="font-weight:600;color:#374151;font-size:0.9rem;">Free pickup scheduled</div>
                    <div style="font-size:0.8rem;color:#9ca3af;">We'll contact you shortly to confirm the pickup time.</div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4 flex-wrap">
                <a href="{{ route('customer.order.builder') }}" wire:navigate
                   class="btn btn-primary fw-semibold px-4" style="border-radius:10px;">
                    Place Another Order
                </a>
                <a href="{{ route('customer.dashboard') }}" wire:navigate
                   class="btn btn-outline-secondary px-4" style="border-radius:10px;">
                    My Dashboard
                </a>
            </div>
        </div>
    </div>

</div>
</div>

@include(theme_view('partials.footer'))
@endsection
