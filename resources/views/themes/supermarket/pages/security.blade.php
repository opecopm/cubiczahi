@extends('themes.supermarket.layouts.app')

@section('header')
    @include('themes.supermarket.partials.account-page-header', [
        'title' => __('account.security_title'),
        'subtitle' => __('account.security_subtitle'),
        'icon' => '🔐',
        'breadcrumbVariant' => 'light',
        'breadcrumb' => [
            ['label' => __('account.dashboard'), 'url' => lroute('customer.dashboard')],
            ['label' => __('account.security')],
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
                <h3 class="mb-1" style="font-weight: 700; color: #064e3b;">Security</h3>
                <p class="text-muted mb-4">Update your password and account protection settings</p>

                <livewire:customer.security-settings />
            </div>
        </div>
    </div>
</div>
@endsection
