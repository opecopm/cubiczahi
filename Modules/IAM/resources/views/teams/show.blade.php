@extends('admin.layouts.app')
@section('subnav')
@include('iam::livewire.partials.iam-nav-tabs')
@endsection
@section('content')
@livewire('iam::teams.show', ['teamId' => $id])
@endsection
