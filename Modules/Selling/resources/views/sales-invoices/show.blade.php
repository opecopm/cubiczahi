
@extends('admin.layouts.app')

@section('subnav')
@include('selling::livewire.partials.selling-nav-tabs')
@endsection

@section('content')
@livewire('selling::sales-invoices.show',['salesInvoiceId'=>$id])
@endsection
