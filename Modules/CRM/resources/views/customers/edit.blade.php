@extends('admin.layouts.app')
@section('subnav')
@include('crm::livewire.partials.crm-nav-tabs')
@endsection
@section('content')
@livewire('crm::customers.edit',['customerId'=>$id])
@endsection
