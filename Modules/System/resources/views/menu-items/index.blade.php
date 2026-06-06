@extends('admin.layouts.app')
@section('subnav')
@include('system::livewire.partials.system-nav-tabs')
@endsection
@section('content')
@livewire('system::menu-items.index')
@endsection
