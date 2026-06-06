<?php

return [
    'name' => 'MediaGallery',
    'collection_name' => env('MEDIA_GALLERY_COLLECTION', 'original'),
    'default_disk' => env('MEDIA_GALLERY_DISK', env('FILESYSTEM_DISK', 'local')),
    'default_visibility' => env('MEDIA_GALLERY_VISIBILITY', 'public'),
];
