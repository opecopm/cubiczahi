@extends('admin.layouts.app')
@section('subnav')
@include('iam::livewire.partials.iam-nav-tabs')
@endsection
@section('content')
@livewire('iam::permissions.index')
@endsection
