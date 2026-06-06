@extends('themes.supermarket.layouts.app')

@section('header')
    @include('themes.supermarket.partials.account-page-header', [
        'title' => __('account.addresses_title'),
        'subtitle' => __('account.addresses_subtitle'),
        'icon' => '📍',
        'breadcrumbVariant' => 'light',
        'breadcrumb' => [
            ['label' => __('account.dashboard'), 'url' => lroute('customer.dashboard')],
            ['label' => __('account.addresses')],
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
                <livewire:customer.address-manager />
            </div>
        </div>
    </div>
</div>
@endsection
