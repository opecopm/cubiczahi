<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * HasMediaManagement Trait
 *
 * Provides helper methods for easy media manager integration in your Livewire components
 *
 * Usage in your component:
 * use App\Traits\HasMediaManagement;
 *
 * class YourComponent extends Component {
 *     use HasMediaManagement;
 *
 *     public function mount($itemId) {
 *         $this->initializeMediaManager('item', $itemId, 'image');
 *     }
 * }
 */
trait HasMediaManagement
{
    public ?int $mediaManagerEntityId = null;
    public string $mediaManagerEntityType = 'item';
    public string $mediaManagerMediaType = 'image';
    public bool $mediaManagerAllowMultiple = true;
    public string $mediaManagerAcceptedFormats = 'image/*';
    public int $mediaManagerMaxFileSize = 2048;
    public ?string $mediaManagerTitle = null;
    public ?string $mediaManagerDescription = null;
    public bool $mediaManagerAllowPrimary = true;

    /**
     * Initialize media manager with default settings
     */
    public function initializeMediaManager(
        string $entityType,
        int $entityId,
        string $mediaType = 'image',
        array $options = []
    ): void {
        $this->mediaManagerEntityType = $entityType;
        $this->mediaManagerEntityId = $entityId;
        $this->mediaManagerMediaType = $mediaType;

        foreach ($options as $key => $value) {
            $property = "mediaManager" . str_replace('_', '', ucwords($key, '_'));
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * Get media manager component parameters
     */
    public function getMediaManagerParams(): array
    {
        return [
            'entityType' => $this->mediaManagerEntityType,
            'entityId' => $this->mediaManagerEntityId,
            'mediaType' => $this->mediaManagerMediaType,
            'allowMultiple' => $this->mediaManagerAllowMultiple,
            'acceptedFormats' => $this->mediaManagerAcceptedFormats,
            'maxFileSize' => $this->mediaManagerMaxFileSize,
            'title' => $this->mediaManagerTitle,
            'description' => $this->mediaManagerDescription,
            'allowPrimary' => $this->mediaManagerAllowPrimary,
        ];
    }

    /**
     * Get livewire include tag for media manager
     */
    public function renderMediaManager(): string
    {
        return view('livewire._media-manager-include', [
            'params' => $this->getMediaManagerParams(),
        ])->render();
    }

    /**
     * Handle media deleted event
     */
    #[\Livewire\Attributes\On('media-deleted')]
    public function onMediaDeleted($id): void
    {
        // Override in your component if needed
    }

    /**
     * Handle media uploaded event
     */
    #[\Livewire\Attributes\On('media-uploaded')]
    public function onMediaUploaded($entityId): void
    {
        // Override in your component if needed
    }

    /**
     * Handle primary media changed event
     */
    #[\Livewire\Attributes\On('media-primary-changed')]
    public function onMediaPrimaryChanged($id): void
    {
        // Override in your component if needed
    }
}
