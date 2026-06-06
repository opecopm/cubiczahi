<?php

namespace Modules\MediaGallery\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\MediaGallery\Models\MediaAsset;

/**
 * WordPress-style Media Picker Component
 *
 * Allows browsing, searching, selecting, and uploading media from the global library.
 * Integrates with existing MediaAsset and MediaLink models.
 *
 * Usage:
 * @livewire('mediagallery::media-picker', [
 *     'allowedTypes' => ['image'],
 *     'multiple' => true,
 *     'maxSelection' => 10,
 *     'buttonText' => 'Select Images'
 * ])
 */
class MediaPicker extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Configuration
    public array $allowedTypes = ['image']; // 'image', 'video', 'audio', 'document'
    public bool $multiple = true;
    public int $maxSelection = 0; // 0 = unlimited
    public string $buttonText = 'Select Media';
    public ?string $usage = 'gallery'; // Usage context for MediaLink
    public ?string $entityType = null; // Type of entity linking to (e.g., 'item', 'variant')
    public ?int $entityId = null; // ID of entity linking to
    public bool $showButton = true;
    public bool $autoOpen = false;

    // State
    public bool $showPickerModal = false;
    public ?bool $forceOpen = null; // External control for modal visibility
    public array $selectedMediaIds = [];
    public string $search = '';
    public string $filterKind = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 24;

    // Upload
    public $newFiles = [];
    public bool $uploading = false;

    // For returning selection
    protected $listeners = [
        'openMediaPicker' => 'openPicker',
        'closeMediaPicker' => 'closePicker',
    ];

    public function mount(): void
    {
        if ($this->autoOpen) {
            $this->showPickerModal = true;
            $this->resetPage();
        }
    }

    public function openPicker(array $params = [])
    {
        if (isset($params['allowedTypes'])) {
            $this->allowedTypes = $params['allowedTypes'];
        }
        if (isset($params['multiple'])) {
            $this->multiple = $params['multiple'];
        }
        if (isset($params['maxSelection'])) {
            $this->maxSelection = $params['maxSelection'];
        }
        if (isset($params['usage'])) {
            $this->usage = $params['usage'];
        }
        if (isset($params['entityType'])) {
            $this->entityType = $params['entityType'];
        }
        if (isset($params['entityId'])) {
            $this->entityId = $params['entityId'];
        }

        $this->showPickerModal = true;
        $this->resetPage();
    }

    public function closePicker()
    {
        $this->showPickerModal = false;
        $this->reset(['selectedMediaIds', 'search', 'filterKind']);

        // Dispatch event to parent to close
        $this->dispatch('mediaPickerClosed')->to('media-manager');
    }

    /**
     * Check if modal should be shown (supports external control)
     */
    public function isModalVisible(): bool
    {
        // If forceOpen is set (from parent), use it
        if ($this->forceOpen !== null) {
            return $this->forceOpen;
        }
        // Otherwise use internal state
        return $this->showPickerModal;
    }

    public function getMediaQueryProperty()
    {
        $query = MediaAsset::query()
            ->where('status', 'active')
            ->where('visibility', 'public');

        // Filter by kind/type
        if (!empty($this->filterKind)) {
            $query->where('kind', $this->filterKind);
        } elseif (!empty($this->allowedTypes)) {
            $query->whereIn('kind', $this->allowedTypes);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhere('alt_text', 'like', '%' . $this->search . '%')
                    ->orWhere('tags', 'like', '%' . $this->search . '%');
            });
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function getMediaProperty()
    {
        return $this->mediaQuery->paginate($this->perPage);
    }

    public function getSelectedMediaProperty()
    {
        if (empty($this->selectedMediaIds)) {
            return collect();
        }

        return MediaAsset::whereIn('id', $this->selectedMediaIds)->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterKind()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function toggleMediaSelection(int $mediaId)
    {
        \Illuminate\Support\Facades\Log::info("MediaPicker::toggleMediaSelection called with ID: {$mediaId}. Multiple mode: " . ($this->multiple ? 'yes' : 'no'));

        // Force convert to boolean just in case it was passed as string 'false' / 'true'
        $isMultiple = filter_var($this->multiple, FILTER_VALIDATE_BOOLEAN);

        if (!$isMultiple && count($this->selectedMediaIds) >= 1) {
            // Single selection mode - replace
            $this->selectedMediaIds = [$mediaId];
            \Illuminate\Support\Facades\Log::info("MediaPicker selection replaced to: " . json_encode($this->selectedMediaIds));
            return;
        }

        if (in_array($mediaId, $this->selectedMediaIds)) {
            // Deselect
            $this->selectedMediaIds = array_values(array_filter(
                $this->selectedMediaIds,
                fn($id) => $id !== $mediaId
            ));
            \Illuminate\Support\Facades\Log::info("MediaPicker item deselected. New selection: " . json_encode($this->selectedMediaIds));
        } else {
            // Check max selection
            if ($this->maxSelection > 0 && count($this->selectedMediaIds) >= $this->maxSelection) {
                session()->flash('warning', 'Maximum selection reached (' . $this->maxSelection . ')');
                return;
            }

            // Select
            $this->selectedMediaIds[] = $mediaId;
            \Illuminate\Support\Facades\Log::info("MediaPicker item selected. New selection: " . json_encode($this->selectedMediaIds));
        }
    }

    public function isSelected(int $mediaId): bool
    {
        return in_array($mediaId, $this->selectedMediaIds);
    }

    public function selectAll()
    {
        $media = $this->mediaQuery->pluck('id')->toArray();

        if ($this->maxSelection > 0) {
            $media = array_slice($media, 0, $this->maxSelection);
        }

        $this->selectedMediaIds = $media;
    }

    public function clearSelection()
    {
        $this->selectedMediaIds = [];
    }

    public function uploadNewFiles(): void
    {
        if (empty($this->newFiles)) {
            return;
        }

        $this->uploading = true;

        try {
            foreach ($this->newFiles as $file) {
                $media = new MediaAsset();
                $media->name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $media->title = $media->name;
                $media->disk = 'public';
                $media->visibility = 'public';
                $media->status = 'active';

                // Determine kind from mime type
                $mimeType = $file->getMimeType();
                if (str_starts_with($mimeType, 'image/')) {
                    $media->kind = 'image';
                    $media->mime_type = $mimeType;
                    $media->extension = $file->getClientOriginalExtension();

                    // Get image dimensions if possible
                    if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                        $imageInfo = @getimagesize($file->getRealPath());
                        if ($imageInfo) {
                            $media->width = $imageInfo[0];
                            $media->height = $imageInfo[1];
                        }
                    }
                } elseif (str_starts_with($mimeType, 'video/')) {
                    $media->kind = 'video';
                } elseif (str_starts_with($mimeType, 'audio/')) {
                    $media->kind = 'audio';
                } else {
                    $media->kind = 'document';
                }

                $media->size = $file->getSize();
                $media->save();

                // Add to Spatie media collection
                $media->addMedia($file->getRealPath())
                    ->preservingOriginal()
                    ->toMediaCollection('media', 'public');
            }

            session()->flash('success', count($this->newFiles) . ' file(s) uploaded successfully!');
            $this->newFiles = [];
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Upload failed: ' . $e->getMessage());
            \Log::error('Media picker upload error: ' . $e->getMessage());
        } finally {
            $this->uploading = false;
        }
    }

    public function confirmSelection()
    {
        if (empty($this->selectedMediaIds)) {
            session()->flash('warning', 'Please select at least one media item.');
            return;
        }

        $selectedMedia = $this->selectedMedia;

        // Dispatch event with selected media
        $this->dispatch('mediaSelected', [
            'media' => $selectedMedia->toArray(),
            'mediaIds' => $this->selectedMediaIds,
            'usage' => $this->usage,
            'entityType' => $this->entityType,
            'entityId' => $this->entityId,
        ]);

        $this->closePicker();
    }

    public function removeFromSelection(int $mediaId)
    {
        $this->selectedMediaIds = array_values(array_filter(
            $this->selectedMediaIds,
            fn($id) => $id !== $mediaId
        ));
    }

    public function getKindsProperty(): array
    {
        return MediaAsset::query()
            ->where('status', 'active')
            ->where('visibility', 'public')
            ->whereIn('kind', $this->allowedTypes)
            ->select('kind')
            ->distinct()
            ->pluck('kind')
            ->toArray();
    }

    public function render()
    {
        return view('mediagallery::livewire.media-picker', [
            'media' => $this->media,
            'selectedMedia' => $this->selectedMedia,
            'kinds' => $this->kinds,
            'hasSelection' => !empty($this->selectedMediaIds),
            'selectionCount' => count($this->selectedMediaIds),
        ]);
    }
}
