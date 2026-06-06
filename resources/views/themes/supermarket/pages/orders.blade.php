@extends('themes.supermarket.layouts.app')

@section('header')
    @include('themes.supermarket.partials.account-page-header', [
        'title' => __('account.orders_title'),
        'subtitle' => __('account.orders_subtitle'),
        'icon' => '📦',
        'breadcrumbVariant' => 'light',
        'breadcrumb' => [
            ['label' => __('account.dashboard'), 'url' => lroute('customer.dashboard')],
            ['label' => __('account.orders')],
        ],
    ])
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        @include('themes.supermarket.partials.account-sidebar')
    </div>
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-1" style="font-weight: 700; color: #064e3b;">Order History</h3>
                <p class="text-muted mb-4">Track and manage your product orders</p>

                @if(count($orders))
                    <div class="row g-3">
                        @foreach($orders as $order)
                            <div class="col-md-6">
                                <a href="{{ lroute('customer.orders.show', $order->id) }}" wire:navigate class="text-decoration-none d-block">
                                <div class="card border-1" style="border-color: #e5e7eb; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 16px rgba(5,150,105,0.15)'" onmouseout="this.style.boxShadow='none'">
                                    <div class="card-body">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                            <div>
                                                <div style="font-weight: 700; color: #064e3b; font-size: 1.05rem;">{{ $order->reference ?? ('#'.$order->id) }}</div>
                                                <small class="text-muted">{{ $order->created_at?->format('M d, Y') ?? '—' }}</small>
                                            </div>
                                            <span style="background: #d1fae5; color: #059669; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">{{ $order->status ?? 'Pending' }}</span>
                                        </div>
                                        <div style="padding: 12px 0; border-top: 1px solid #f1f3f5; border-bottom: 1px solid #f1f3f5; margin-bottom: 12px;">
                                            <small class="text-muted">{{ count($order->items ?? []) }} item(s)</small>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span class="text-muted">Total:</span>
                                            <span style="font-weight: 700; font-size: 1.2rem; color: #059669;">{{ number_format($order->total ?? 0, 2) }} SAR</span>
                                        </div>
                                    </div>
                                </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 20px; background: #f0fdf4; border-radius: 12px;">
                        <div style="font-size: 3rem; margin-bottom: 12px;">📦</div>
                        <p class="text-muted mb-3">You haven't placed any orders yet</p>
                        <a href="{{ lroute('catalog.index') }}" wire:navigate class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                            Browse Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
