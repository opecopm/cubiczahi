@extends('themes.supermarket.layouts.app')

@section('header')
    <h2 class="page-title">Order Details</h2>
@endsection

@section('breadcrumb')
    @include('themes.supermarket.partials.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard', 'url' => lroute('customer.dashboard')],
            ['label' => 'Orders', 'url' => lroute('customer.orders.index')],
            ['label' => $order->reference ?? ('#'.$order->id)],
        ],
    ])
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        @include('themes.supermarket.partials.account-sidebar')
    </div>
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
                    <div>
                        <a href="{{ lroute('customer.orders.index') }}" wire:navigate class="text-decoration-none" style="font-weight: 600; color: #059669;">
                            &larr; Back to orders
                        </a>
                        <h3 class="mt-3 mb-1" style="font-weight: 800; color: #064e3b;">
                            {{ $order->reference ?? ('#'.$order->id) }}
                        </h3>
                        <p class="text-muted mb-0">
                            Placed {{ $order->order_date ? \Illuminate\Support\Carbon::parse($order->order_date)->format('M d, Y') : $order->created_at?->format('M d, Y') }}
                        </p>
                    </div>
                    <span style="background: #d1fae5; color: #059669; padding: 8px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700;">
                        {{ $order->status ?? 'Pending' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 style="font-weight: 700; color: #064e3b; margin-bottom: 16px;">Items</h5>

                        @forelse($order->items as $item)
                            <div class="py-3" style="border-bottom: 1px solid #f1f3f5;">
                                <div class="d-flex justify-content-between gap-3 align-items-start">
                                    <div>
                                        <div style="font-weight: 700; color: #064e3b;">{{ $item->name }}</div>
                                        @if($item->description)
                                            <div class="text-muted mt-1" style="font-size: 0.88rem;">{{ $item->description }}</div>
                                        @endif
                                        <div class="text-muted mt-1" style="font-size: 0.88rem;">
                                            Qty {{ $item->quantity }} x {{ number_format($item->price ?? 0, 2) }} {{ $order->currency ?? 'SAR' }}
                                        </div>
                                    </div>
                                    <div style="font-weight: 800; color: #059669; white-space: nowrap;">
                                        {{ number_format($item->total ?? $item->total_price ?? 0, 2) }} {{ $order->currency ?? 'SAR' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted" style="padding: 24px 0;">No items found for this order.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 style="font-weight: 700; color: #064e3b; margin-bottom: 16px;">Summary</h5>

                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Subtotal</span>
                            <strong>{{ number_format($order->subtotal ?? 0, 2) }} {{ $order->currency ?? 'SAR' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Shipping</span>
                            <strong>{{ number_format($order->delivery_fees ?? 0, 2) }} {{ $order->currency ?? 'SAR' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Tax</span>
                            <strong>{{ number_format($order->tax ?? 0, 2) }} {{ $order->currency ?? 'SAR' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-3 mt-2" style="border-top: 1px solid #e5e7eb;">
                            <span style="font-weight: 800; color: #064e3b;">Total</span>
                            <span style="font-weight: 900; color: #059669; font-size: 1.2rem;">
                                {{ number_format($order->total ?? 0, 2) }} {{ $order->currency ?? 'SAR' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 style="font-weight: 700; color: #064e3b; margin-bottom: 16px;">Delivery</h5>
                        <div class="text-muted mb-2" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Method</div>
                        <div style="font-weight: 700; color: #064e3b;">
                            {{ $order->deliveryMethod?->name ?? 'Not selected' }}
                        </div>
                        @if($order->deliveryMethod?->estimated_label)
                            <div class="text-muted mt-1">{{ $order->deliveryMethod->estimated_label }}</div>
                        @endif

                        <div class="text-muted mt-4 mb-2" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Delivery Date</div>
                        <div style="font-weight: 700; color: #064e3b;">
                            {{ $order->delivery_date ? \Illuminate\Support\Carbon::parse($order->delivery_date)->format('M d, Y') : 'To be scheduled' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
