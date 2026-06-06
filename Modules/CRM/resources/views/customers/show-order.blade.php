@extends('admin.layouts.app')

@section('subnav')
    @include('crm::livewire.partials.crm-nav-tabs')
@endsection

@section('content')
    @livewire('selling::sales-orders.show', ['id' => $orderId, 'isEmbedded' => true, 'customerId' => $customer->id])
@endsection
