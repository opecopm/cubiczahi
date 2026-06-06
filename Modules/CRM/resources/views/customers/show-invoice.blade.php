@extends('admin.layouts.app')

@section('subnav')
    @include('crm::livewire.partials.crm-nav-tabs')
@endsection

@section('content')
    @livewire('selling::sales-invoices.show', ['salesInvoiceId' => $invoiceId, 'isEmbedded' => true, 'customerId' => $customer->id])
@endsection
