<?php

namespace Modules\MediaGallery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\MediaGallery\Models\MediaAsset;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaGalleryController extends Controller
{
    public function index()
    {
        return view('mediagallery::media-assets.index');
    }

    public function show(MediaAsset $mediaAsset)
    {
        return view('mediagallery::media-assets.show', compact('mediaAsset'));
    }

    public function preview(MediaAsset $mediaAsset): StreamedResponse
    {
        $media = $this->resolvePrimaryMedia($mediaAsset);

        return Storage::disk($media->disk)->response(
            $media->getPathRelativeToRoot(),
            $media->file_name,
            [
                'Content-Type' => (string) ($media->mime_type ?: 'application/octet-stream'),
                'Content-Disposition' => 'inline; filename="'.$media->file_name.'"',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }

    public function download(MediaAsset $mediaAsset): StreamedResponse
    {
        $media = $this->resolvePrimaryMedia($mediaAsset);

        return Storage::disk($media->disk)->download(
            $media->getPathRelativeToRoot(),
            $media->file_name,
            [
                'Content-Type' => (string) ($media->mime_type ?: 'application/octet-stream'),
            ]
        );
    }

    private function resolvePrimaryMedia(MediaAsset $mediaAsset): Media
    {
        $mediaAsset->loadMissing('media');

        $media = $mediaAsset->primaryMedia();
        abort_unless($media, 404);

        return $media;
    }
}
