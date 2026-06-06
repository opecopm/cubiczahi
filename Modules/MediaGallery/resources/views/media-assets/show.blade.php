@extends('admin.layouts.app')
@section('content')
@livewire('mediagallery::media-assets.show', ['id' => $mediaAsset->id])
@endsection
