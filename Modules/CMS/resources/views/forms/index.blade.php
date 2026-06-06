@extends('admin.layouts.app')
@section('subnav')
@include('cms::livewire.partials.cms-nav-tabs')
@endsection

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="opacity-5 text-dark" href="{{ route('admin.cms.forms.index') }}">Forms</a>
        </li>
        <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
            {{ str_replace('-', ' ', Route::currentRouteName()) }}
        </li>
    </ol>
</nav>
@endsection

@section('content')
@livewire('cms::forms.index')
@endsection
