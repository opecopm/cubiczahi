<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

/**
 * Reusable Media Manager Component
 *
 * This component handles uploading, linking, and managing media (images, files) for any entity.
 * Can be used for products, variations, items, variants, etc.
 *
 * Usage in your component:
 * @livewire('media-manager', [
 *     'entityId' => $item->id,
 *     'entityType' => 'item',
 *     'mediaType' => 'image',
 *     'allowMultiple' => true,
 *     'acceptedFormats' => 'image/*'
 * ])
 */
class MediaManager extends Component
{
    use WithFileUploads;

    // Configuration
    public string $entityType = 'item'; // 'item', 'variant', 'product', 'asset', etc.
    public int|string $entityId;
    public string $mediaType = 'image'; // 'image', 'document', 'file'
    public bool $allowMultiple = true;
    public string $acceptedFormats = 'image/*'; // 'image/*', '.pdf,.doc,.docx', etc.
    public int $maxFileSize = 2048; // in KB
    public ?string $title = null; // Optional title for the section
    public ?string $description = null; // Optional description
    public bool $allowLinking = false; // Allow linking to existing media
    public bool $allowPrimary = true; // Allow setting as primary
    public bool $allowReorder = false; // Allow reordering via drag-drop
    public ?string $mediaCollection = null; // If using Spatie Media Library

    // State
    public array $media = [];
    public array $newFiles = [];
    public ?string $primaryMediaId = null;
    public ?string $pendingDeleteId = null;
    public ?string $editingMediaId = null;
    public array $editData = [];

    // Linking existing media
    public array $availableMedia = [];
    public array $selectedMediaIds = [];
    public bool $showLinkingForm = false;
    public ?int $parentItemId = null; // ID of parent item (for variants)

    // WordPress-style Media Picker
    public bool $showMediaPicker = false;
    public array $mediaPickerConfig = [];

    // UI State
    public bool $showUploadForm = true;
    public bool $showExistingMedia = true;
    public string $viewMode = 'grid'; // 'grid', 'list'

    public string $ui = 'default'; // 'default', 'compact'


    protected $listeners = ['mediaDeleted' => 'refresh'];

    public function mount()
    {
        // Enable linking for variants by default
        if ($this->entityType === 'variant' && !isset($this->allowLinking)) {
            $this->allowLinking = true;
        }

        $this->loadMedia();

        if ($this->allowLinking && $this->entityType === 'variant') {
            $this->loadAvailableMedia();
        }

        // Configure media picker
        $this->mediaPickerConfig = [
            'allowedTypes' => $this->getMediaPickerTypes(),
            'multiple' => $this->allowMultiple,
            'maxSelection' => 0,
            'usage' => $this->entityType,
            'entityType' => $this->entityType,
            'entityId' => $this->entityId,
        ];
    }

    /**
     * Get media picker types based on media type
     */
    protected function getMediaPickerTypes(): array
    {
        return match ($this->mediaType) {
            'image' => ['image'],
            'video' => ['video'],
            'audio' => ['audio'],
            'document' => ['document'],
            default => ['image', 'document'],
        };
    }

    /**
     * Load media from database
     */
    public function loadMedia(): void
    {
        $this->media = $this->getMediaFromDatabase();
        $this->determinePrimaryMedia();
    }

    /**
     * Get media from database based on entity type
     */
    protected function getMediaFromDatabase(): array
    {
        $mediaClass = $this->getMediaModelClass();
        if (!$mediaClass) {
            return [];
        }

        $query = $mediaClass::query()
            ->where($this->getEntityForeignKey(), $this->entityId);

        if (method_exists($mediaClass, 'orderBy')) {
            $query->orderBy('display_order', 'asc');
        }

        return $query->get()->map(fn ($item) => [
            'id' => $item->id,
            'path' => $this->getMediaPath($item),
            'url' => $this->getMediaUrl($item),
            'name' => $item->name ?? $item->title ?? basename($item->path ?? ''),
            'title' => $item->title ?? null,
            'description' => $item->description ?? null,
            'is_primary' => $item->is_primary ?? false,
            'display_order' => $item->display_order ?? 0,
            'created_at' => $item->created_at ?? null,
            'type' => $this->mediaType,
        ])->toArray();
    }

    /**
     * Get the appropriate media model class
     */
    protected function getMediaModelClass(): ?string
    {
        $models = [
            'item_image' => 'Modules\Inventory\Models\ItemImage',
            'variant_image' => 'Modules\Inventory\Models\VariantImage',
        ];

        $key = "{$this->entityType}_{$this->mediaType}";
        return $models[$key] ?? null;
    }

    /**
     * Get foreign key for the entity
     */
    protected function getEntityForeignKey(): string
    {
        return match ($this->entityType) {
            'item' => 'item_id',
            'variant' => 'variant_id',
            'product' => 'product_id',
            default => "{$this->entityType}_id",
        };
    }

    /**
     * Get media path
     */
    protected function getMediaPath($item): string
    {
        return $item->path ?? '';
    }

    /**
     * Get media URL
     */
    protected function getMediaUrl($item): string
    {
        $path = $this->getMediaPath($item);
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return Storage::disk('public')->url($path);
    }

    /**
     * Determine primary media
     */
    protected function determinePrimaryMedia(): void
    {
        $primary = collect($this->media)->firstWhere('is_primary', true);
        $this->primaryMediaId = $primary['id'] ?? null;

        if (!$this->primaryMediaId && !empty($this->media)) {
            $this->primaryMediaId = $this->media[0]['id'] ?? null;
        }
    }

    /**
     * Validate and upload files
     */
    public function uploadMedia(): void
    {
        $this->validate([
            'newFiles.*' => "required|file|max:{$this->maxFileSize}",
        ]);

        foreach ($this->newFiles as $file) {
            $this->saveMediaToDatabase($file);
        }

        $this->newFiles = [];
        $this->loadMedia();
        $this->dispatch('media-uploaded', ['entityId' => $this->entityId]);
    }

    /**
     * Save media to database
     */
    protected function saveMediaToDatabase($file): void
    {
        $mediaClass = $this->getMediaModelClass();
        if (!$mediaClass) {
            return;
        }

        $path = $file->store(
            "{$this->entityType}-{$this->mediaType}s/{$this->entityId}",
            'public'
        );

        $order = (new $mediaClass)::where(
            $this->getEntityForeignKey(),
            $this->entityId
        )->max('display_order') ?? -1;

        $mediaClass::create([
            $this->getEntityForeignKey() => $this->entityId,
            'path' => $path,
            'display_order' => ++$order,
            'is_primary' => $order === 0,
        ]);
    }

    /**
     * Delete media
     */
    public function deleteMedia(int|string $id): void
    {
        $mediaClass = $this->getMediaModelClass();
        if (!$mediaClass) {
            return;
        }

        $media = $mediaClass::find($id);
        if ($media && $media->{$this->getEntityForeignKey()} == $this->entityId) {
            $path = (string) ($media->path ?? '');
            if ($path !== '' && $this->shouldDeletePhysicalFile($media)) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $media->delete();
        }

        $this->loadMedia();
        $this->dispatch('media-deleted', ['id' => $id]);
    }

    protected function shouldDeletePhysicalFile(Model $media): bool
    {
        $path = (string) ($media->path ?? '');
        if ($path === '') {
            return false;
        }

        if ($this->entityType === 'variant' && class_exists(\Modules\Inventory\Models\VariantImage::class)) {
            if ($media instanceof \Modules\Inventory\Models\VariantImage && filled($media->item_image_id)) {
                return false;
            }
        }

        $ownedPrefix = "{$this->entityType}-{$this->mediaType}s/{$this->entityId}/";
        if (! str_starts_with($path, $ownedPrefix)) {
            return false;
        }

        return true;
    }

    /**
     * Set as primary media
     */
    public function setPrimary(int|string $id): void
    {
        $mediaClass = $this->getMediaModelClass();
        if (!$mediaClass) {
            return;
        }

        $mediaClass::where($this->getEntityForeignKey(), $this->entityId)
            ->update(['is_primary' => false]);

        $mediaClass::find($id)?->update(['is_primary' => true]);

        $this->loadMedia();
        $this->dispatch('media-primary-changed', ['id' => $id]);
    }

    /**
     * Edit media metadata
     */
    public function editMedia(int|string $id): void
    {
        $this->editingMediaId = $id;
        $media = collect($this->media)->firstWhere('id', $id);
        if ($media) {
            $this->editData = [
                'title' => $media['title'],
                'description' => $media['description'],
            ];
        }
    }

    /**
     * Save media edits
     */
    public function saveMediaEdit(): void
    {
        $mediaClass = $this->getMediaModelClass();
        if (!$mediaClass || !$this->editingMediaId) {
            return;
        }

        $mediaClass::find($this->editingMediaId)?->update($this->editData);

        $this->editingMediaId = null;
        $this->editData = [];
        $this->loadMedia();
    }

    /**
     * Cancel editing
     */
    public function cancelEdit(): void
    {
        $this->editingMediaId = null;
        $this->editData = [];
    }

    /**
     * Refresh media list
     */
    public function refresh(): void
    {
        $this->loadMedia();
    }

    /**
     * Toggle view mode
     */
    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    /**
     * Load available media from parent item (for linking to variants)
     */
    public function loadAvailableMedia(): void
    {
        if ($this->entityType !== 'variant') {
            return;
        }

        try {
            // Get the variant to find its item_id
            $variant = \Modules\Inventory\Models\ItemVariant::find($this->entityId);
            if (!$variant) {
                return;
            }

            $this->parentItemId = $variant->item_id;

            // Get all images from the parent item
            $itemImages = \Modules\Inventory\Models\ItemImage::where('item_id', $this->parentItemId)
                ->orderBy('display_order')
                ->get();

            // Get already linked image IDs
            $linkedImageIds = \Modules\Inventory\Models\VariantImage::where('variant_id', $this->entityId)
                ->pluck('item_image_id')
                ->toArray();

            $this->availableMedia = $itemImages->map(fn ($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'url' => Storage::disk('public')->url($img->path),
                'name' => $img->title ?? basename($img->path),
                'title' => $img->title,
                'is_linked' => in_array($img->id, $linkedImageIds),
            ])->toArray();
        } catch (\Exception $e) {
            // Silently fail if tables don't exist or variant not found
            $this->availableMedia = [];
        }
    }

    /**
     * Toggle media selection for linking
     */
    public function toggleMediaSelection(int $mediaId): void
    {
        if (in_array($mediaId, $this->selectedMediaIds)) {
            $this->selectedMediaIds = array_diff($this->selectedMediaIds, [$mediaId]);
        } else {
            $this->selectedMediaIds[] = $mediaId;
        }
    }

    /**
     * Link selected media to current variant
     */
    public function linkSelectedMedia(): void
    {
        if (empty($this->selectedMediaIds)) {
            session()->flash('warning', 'Please select at least one image to link.');
            return;
        }

        try {
            $order = \Modules\Inventory\Models\VariantImage::where('variant_id', $this->entityId)
                ->max('display_order') ?? -1;

            foreach ($this->selectedMediaIds as $itemImageId) {
                // Check if already linked
                $exists = \Modules\Inventory\Models\VariantImage::where('variant_id', $this->entityId)
                    ->where('item_image_id', $itemImageId)
                    ->exists();

                if (!$exists) {
                    $itemImage = \Modules\Inventory\Models\ItemImage::find($itemImageId);
                    if ($itemImage) {
                        \Modules\Inventory\Models\VariantImage::create([
                            'variant_id' => $this->entityId,
                            'item_image_id' => $itemImageId,
                            'path' => $itemImage->path,
                            'display_order' => ++$order,
                            'is_primary' => $order === 0,
                        ]);
                    }
                }
            }

            session()->flash('message', 'Images linked successfully!');
            $this->selectedMediaIds = [];
            $this->showLinkingForm = false;
            $this->loadMedia();
            $this->loadAvailableMedia();
            $this->dispatch('media-linked', ['entityId' => $this->entityId]);
        } catch (\Exception $e) {
            session()->flash('error', 'Error linking images: ' . $e->getMessage());
            \Log::error('Error linking media: ' . $e->getMessage());
        }
    }

    /**
     * Toggle linking form visibility
     */
    public function toggleLinkingForm(): void
    {
        $this->showLinkingForm = !$this->showLinkingForm;
    }

    /**
     * Open WordPress-style Media Picker
     */
    public function openMediaPicker(): void
    {
        $this->showMediaPicker = true;
    }

    /**
     * Close Media Picker
     */
    #[On('mediaPickerClosed')]
    public function closeMediaPicker(): void
    {
        $this->showMediaPicker = false;
    }

    /**
     * Handle media selected from WordPress-style picker
     */
    #[On('mediaSelected')]
    public function onMediaSelected(array $data): void
    {
        $mediaIds = $data['mediaIds'] ?? [];
        $usage = $data['usage'] ?? 'gallery';

        if (empty($mediaIds)) {
            return;
        }

        try {
            if (
                isset($data['entityType'])
                && filled($data['entityType'])
                && (string) $data['entityType'] !== (string) $this->entityType
            ) {
                return;
            }

            if (
                isset($data['entityId'])
                && filled($data['entityId'])
                && (string) $data['entityId'] !== (string) $this->entityId
            ) {
                return;
            }

            $mediaClass = $this->getMediaModelClass();

            if (!$mediaClass) {
                session()->flash('error', 'This entity type is not configured to receive media.');
                return;
            }

            $collectionName = (string) config('mediagallery.collection_name', 'original');

            $mediaAssets = \Modules\MediaGallery\Models\MediaAsset::whereIn('id', $mediaIds)->get();

            $order = (new $mediaClass)::where($this->getEntityForeignKey(), $this->entityId)
                ->max('display_order') ?? -1;

            $linkedCount = 0;

            foreach ($mediaAssets as $mediaAsset) {
                $spatieMedia = $mediaAsset->getFirstMedia($collectionName) ?: $mediaAsset->media()->first();

                if (!$spatieMedia) {
                    continue;
                }

                $path = (string) $spatieMedia->getPathRelativeToRoot();

                $exists = (new $mediaClass)::where($this->getEntityForeignKey(), $this->entityId)
                    ->where('path', $path)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $mediaClass::create([
                    $this->getEntityForeignKey() => $this->entityId,
                    'path' => $path,
                    'display_order' => ++$order,
                    'is_primary' => $order === 0,
                ]);

                $linkedCount++;
            }

            if ($linkedCount > 0) {
                session()->flash('message', $linkedCount . ' media added successfully!');
            } else {
                session()->flash('warning', 'No new media was added (already linked or unavailable).');
            }

            $this->showMediaPicker = false;
            $this->loadMedia();
            $this->dispatch('media-linked', ['entityId' => $this->entityId]);
        } catch (\Exception $e) {
            session()->flash('error', 'Error linking media: ' . $e->getMessage());
            \Log::error('Error linking media from picker: ' . $e->getMessage());
        }
    }

    /**
     * Get entity class name for polymorphic relation
     */
    protected function getEntityClass(): string
    {
        return match ($this->entityType) {
            'item' => 'Modules\Inventory\Models\Item',
            'variant' => 'Modules\Inventory\Models\ItemVariant',
            'product' => 'Modules\Selling\Models\Product',
            default => 'Modules\Inventory\Models\Item',
        };
    }

    public function render()
    {
        return view('livewire.media-manager', [
            'hasMedia' => !empty($this->media),
            'totalMedia' => count($this->media),
            'hasAvailableMedia' => !empty($this->availableMedia),
            'totalAvailableMedia' => count($this->availableMedia),
            'mediaPickerConfig' => $this->mediaPickerConfig,
        ]);
    }
}
