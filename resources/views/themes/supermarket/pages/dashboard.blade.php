@extends('themes.supermarket.layouts.app')

@section('header')
    @include('themes.supermarket.partials.account-page-header', [
        'title' => __('account.dash_title'),
        'subtitle' => __('account.dash_subtitle'),
        'icon' => '🧴',
        'breadcrumbVariant' => 'light',
        'breadcrumb' => [
            ['label' => __('account.dashboard')],
        ],
    ])
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        @include('themes.supermarket.partials.account-sidebar')
    </div>
    <div class="col-lg-9">
        {{-- Welcome Card --}}
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #064e3b 100%); color: white; margin-bottom: 24px;">
            <div class="card-body p-5">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-weight: 800; margin-bottom: 8px;">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                        <p style="opacity: 0.9; margin: 0; font-size: 0.95rem;">Manage your orders, profile, and delivery addresses all in one place</p>
                    </div>
                    <div style="font-size: 3rem;">🧴</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Quick Stats --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(6, 78, 59, 0.05)); border-left: 4px solid #059669;">
                    <div class="card-body p-4">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <small class="text-muted" style="font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Orders</small>
                                <h3 style="font-weight: 800; color: #064e3b; margin-top: 8px;">{{ $ordersCount }}</h3>
                            </div>
                            <div style="font-size: 2rem;">📦</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05)); border-left: 4px solid #f59e0b;">
                    <div class="card-body p-4">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <small class="text-muted" style="font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Saved Addresses</small>
                                <h3 style="font-weight: 800; color: #064e3b; margin-top: 8px;">{{ $addressesCount }}</h3>
                            </div>
                            <div style="font-size: 2rem;">📍</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 style="font-weight: 700; color: #064e3b; margin-bottom: 16px;">Quick Actions</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ lroute('catalog.index') }}" wire:navigate class="btn btn-primary" style="border-radius: 8px; padding: 12px 24px; font-weight: 600;">
                        🛒 Shop Products
                    </a>
                    <a href="{{ lroute('customer.profile') }}" wire:navigate class="btn btn-outline-primary" style="border-radius: 8px; padding: 12px 24px; font-weight: 600;">
                        ✏️ Edit Profile
                    </a>
                    <a href="{{ lroute('customer.addresses.index') }}" wire:navigate class="btn btn-outline-primary" style="border-radius: 8px; padding: 12px 24px; font-weight: 600;">
                        📍 Manage Addresses
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 style="font-weight: 700; color: #064e3b; margin-bottom: 16px;">Recent Orders</h5>
                @if($recentOrders->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #f0fdf4; border-top: 1px solid #e5e7eb;">
                                <tr>
                                    <th style="font-weight: 700; color: #6b7280; font-size: 0.8rem; text-transform: uppercase; padding: 12px 16px;">Order</th>
                                    <th style="font-weight: 700; color: #6b7280; font-size: 0.8rem; text-transform: uppercase; padding: 12px 16px;">Date</th>
                                    <th style="font-weight: 700; color: #6b7280; font-size: 0.8rem; text-transform: uppercase; padding: 12px 16px;">Status</th>
                                    <th style="font-weight: 700; color: #6b7280; font-size: 0.8rem; text-transform: uppercase; padding: 12px 16px; text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 16px; font-weight: 600;">
                                            <a href="{{ lroute('customer.orders.show', $order->id) }}" wire:navigate class="text-decoration-none" style="color: #064e3b;">
                                                {{ $order->reference ?? ('#'.$order->id) }}
                                            </a>
                                        </td>
                                        <td style="padding: 16px; color: #6b7280;">{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                                        <td style="padding: 16px;">
                                            <span style="background: #d1fae5; color: #059669; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">{{ $order->status ?? 'Pending' }}</span>
                                        </td>
                                        <td style="padding: 16px; font-weight: 700; color: #059669; text-align: right;">{{ number_format($order->total ?? 0, 2) }} SAR</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="text-align: center; padding: 16px;">
                        <a href="{{ lroute('customer.orders.index') }}" wire:navigate class="btn btn-sm btn-outline-secondary" style="border-radius: 6px; padding: 8px 20px;">
                            View All Orders
                        </a>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; background: #f0fdf4; border-radius: 8px;">
                        <div style="font-size: 2.5rem; margin-bottom: 12px;">📭</div>
                        <p class="text-muted mb-3">No orders yet. Ready to get started?</p>
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
