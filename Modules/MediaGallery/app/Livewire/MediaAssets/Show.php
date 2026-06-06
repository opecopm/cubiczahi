<?php

namespace Modules\MediaGallery\Livewire\MediaAssets;

use Livewire\Component;
use Modules\MediaGallery\Models\MediaAsset;

class Show extends Component
{
    public MediaAsset $asset;

    public function mount(int $id): void
    {
        $this->asset = MediaAsset::query()
            ->with(['media', 'company', 'links.linkable'])
            ->withCount('links')
            ->findOrFail($id);
    }

    public function refreshAsset(): void
    {
        $this->asset->refresh();
        $this->asset->load(['media', 'company', 'links.linkable']);
        $this->asset->loadCount('links');
    }

    public function render()
    {
        return view('mediagallery::livewire.media-assets.show');
    }
}
