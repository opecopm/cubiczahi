@extends('admin.layouts.app')
@section('subnav')
@include('business::livewire.partials.business-nav-tabs')
@endsection
@section('content')
@livewire('business::taxes.index')
@endsection
