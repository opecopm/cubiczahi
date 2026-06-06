@extends('themes.laundry-one.layouts.app')

@section('header')
    @include('themes.laundry-one.partials.account-page-header', [
        'title' => 'Profile',
        'subtitle' => 'Manage your personal details',
        'icon' => '✏️',
        'breadcrumbVariant' => 'light',
        'breadcrumb' => [
            ['label' => 'Dashboard', 'url' => route('customer.dashboard')],
            ['label' => 'Profile'],
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
                <h3 class="mb-1" style="font-weight: 700; color: #0a2463;">Profile Information</h3>
                <p class="text-muted mb-4">Manage your personal details</p>

                <livewire:customer.profile-form />
            </div>
        </div>
    </div>
</div>
@endsection
