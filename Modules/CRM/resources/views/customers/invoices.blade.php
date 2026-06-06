@extends('admin.layouts.app')

@section('subnav')
    @include('crm::livewire.partials.crm-nav-tabs')
@endsection

@section('content')
    @component('admin.partials.page.inner-header', [
        'title' => $customer->name . ' - Sales Invoices',
        'breadcrumbs' => [
            [
                'label' => 'Customers',
                'url' => route('admin.crm.customers.index'),
                'icon' => 'back',
            ],
            [
                'label' => $customer->name,
                'url' => route('admin.crm.customers.show', $customer->id),
                'class' => 'text-body fw-medium',
            ],
            [
                'label' => 'Invoices',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'type' => 'badge',
                'title' => ucfirst($customer->status),
                'class' => $customer->status == 'active' ? 'bg-success-lt' : 'bg-secondary-lt',
            ],
            [
                'title' => 'Customer Profile',
                'route' => 'admin.crm.customers.show',
                'params' => $customer->id,
                'icon' => 'ti ti-arrow-left',
                'class' => 'btn btn-sm btn-outline-secondary',
            ],
            [
                'title' => 'Create Invoice',
                'route' => 'admin.selling.sales-invoices.create',
                'params' => ['customer_id' => $customer->id],
                'icon' => 'ti ti-plus',
                'class' => 'btn btn-sm btn-primary',
            ],
        ],
    ])
        @if($customer->email || $customer->phone)
            @slot('meta')
                @if($customer->email)
                    <span class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l9 6l9 -6l-18 0v13c0 0.552 0.448 1 1 1h16c0.552 0 1 -0.448 1 -1v-13l-18 0"/></svg>
                        {{ $customer->email }}
                    </span>
                @endif
                @if($customer->phone)
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-1.5 1.5a11 11 0 0 0 5 5l1.5 -1.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/></svg>
                        {{ $customer->phone }}
                    </span>
                @endif
            @endslot
        @endif

    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @livewire('selling::sales-invoices.index', ['customerId' => $customer->id])
        </div>
    </div>
@endsection
