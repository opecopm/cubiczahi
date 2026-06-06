@extends('admin.layouts.app')
@section('subnav')
@include('iam::livewire.partials.iam-nav-tabs')
@endsection
@section('content')
@livewire('iam::roles.show', ['roleId' => $id])
@endsection
