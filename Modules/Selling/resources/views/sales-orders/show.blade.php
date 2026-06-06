
@extends('admin.layouts.app')

@section('subnav')
@include('selling::livewire.partials.selling-nav-tabs')
@endsection

@section('content')
@livewire('selling::sales-orders.show', ['id' => $id])
@endsection
