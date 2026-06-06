@extends('themes.laundry-one.layouts.app')

@section('header')
    @include('themes.laundry-one.partials.account-page-header', [
        'title' => 'Addresses',
        'subtitle' => 'Manage your saved delivery addresses',
        'icon' => '📍',
        'breadcrumbVariant' => 'light',
        'breadcrumb' => [
            ['label' => 'Dashboard', 'url' => route('customer.dashboard')],
            ['label' => 'Addresses'],
        ],
    ])
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        @include('themes.laundry-one.partials.account-sidebar')
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
