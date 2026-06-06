<div class="col-auto text-center">
    <div class="avatar avatar-xl position-relative">
        <img src="{{ $item->getFirstMediaUrl('primary_photo') ? $item->getFirstMediaUrl('primary_photo') : url('assets/img/no-photo.jpg') }}"
             alt="item_image"
             class="w-100 rounded-rectangle shadow-sm">
    </div><br>
    <a class="badge badge-secondary" href="#" wire:click="openPhotoModal">
        update
    </a>
</div>

<div class="col-auto my-auto">
    <div class="h-80">
        <h6 class="mb-1">
            {{ $item->name }}
            @if($item->getTranslation('name', system_setting('secondary_language','ar')))
                <br><small>{{ $item->getTranslation('name', system_setting('secondary_language','ar')) }}</small>
            @endif
        </h6>
        <p class="mb-0 font-weight-normal text-sm">
            {{ $item->category->name ?? '' }}
        </p>
    </div>
</div>
