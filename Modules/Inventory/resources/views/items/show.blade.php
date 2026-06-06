@extends('admin.layouts.app')
@section('subnav')
@include('inventory::livewire.partials.inventory-nav-tabs')
@endsection
@section('content')
@livewire('inventory::items.show',['itemId'=>$id])
@endsection
