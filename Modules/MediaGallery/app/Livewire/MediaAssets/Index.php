<?php

namespace Modules\MediaGallery\Livewire\MediaAssets;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\MediaGallery\Models\MediaAsset;
use Modules\MediaGallery\Models\MediaFolder;

class Index extends Component
{
    use WithFileUploads, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage = 24;

    public string $search = '';

    public array $filters = [
        'disk' => '',
        'kind' => '',
        'visibility' => '',
        'status' => '',
    ];

    public string $uploadType = 'image';

    public bool $showImageModal = false;

    public bool $showFolderModal = false;

    public $mediaFile;

    public ?int $assetId = null;

    public ?int $company_id = null;

    public ?int $currentFolderId = null;

    public ?int $createFolderParentId = null;

    public string $name = '';

    public string $title = '';

    public string $alt_text = '';

    public string $description = '';

    public string $disk = '';

    public string $visibility = 'public';

    public string $status = 'active';

    public string $tags = '';

    public string $folderName = '';

    public string $folderDescription = '';

    public bool $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filters' => ['except' => []],
        'currentFolderId' => ['except' => null],
        'sortBy' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 24],
    ];

    public function mount(): void
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->orderable = ['id', 'name', 'disk', 'kind', 'size', 'created_at'];
        $this->disk = $this->defaultDisk();
        $this->company_id = auth()->user()?->defaultCompany()?->id;
    }

    protected function rules(): array
    {
        $diskRules = ['required', 'string', Rule::in($this->availableDisks())];
        $fileRules = $this->uploadType === 'image'
            ? [$this->updateMode ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240']
            : [$this->updateMode ? 'nullable' : 'required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv', 'max:51200'];

        return [
            'mediaFile' => $fileRules,
            'name' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'disk' => $diskRules,
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'status' => ['required', Rule::in(['active', 'archived'])],
            'tags' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openFolder(int $folderId): void
    {
        $folder = MediaFolder::query()->findOrFail($folderId);
        $this->currentFolderId = $folder->id;
        $this->resetPage();
    }

    public function uploadImageToFolder(int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->openImageCreateModal();
    }

    public function uploadDocumentToFolder(int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->openDocumentCreateModal();
    }

    public function goToRoot(): void
    {
        $this->currentFolderId = null;
        $this->resetPage();
    }

    public function goToParentFolder(): void
    {
        $currentFolder = $this->currentFolder();

        if (! $currentFolder) {
            return;
        }

        $this->currentFolderId = $currentFolder->parent_id;
        $this->resetPage();
    }

    public function openImageCreateModal(): void
    {
        $this->resetInputFields();
        $this->uploadType = 'image';
        $this->showModal = false;
        $this->showImageModal = true;
        $this->dispatch('media-gallery-image-modal-opened');
    }

    public function openDocumentCreateModal(): void
    {
        $this->resetInputFields();
        $this->uploadType = 'document';
        $this->showImageModal = false;
        $this->showModal = true;
    }

    public function openFolderCreateModal(): void
    {
        $this->folderName = '';
        $this->folderDescription = '';
        $this->createFolderParentId = $this->currentFolderId;
        $this->showFolderModal = true;
        $this->resetErrorBag();
    }

    public function openChildFolderCreateModal(int $folderId): void
    {
        $this->folderName = '';
        $this->folderDescription = '';
        $this->createFolderParentId = $folderId;
        $this->showFolderModal = true;
        $this->resetErrorBag();
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'disk' => '',
            'kind' => '',
            'visibility' => '',
            'status' => '',
        ];
        $this->search = '';
        $this->resetPage();
    }

    public function resetInputFields(): void
    {
        $this->reset([
            'mediaFile',
            'assetId',
            'name',
            'title',
            'alt_text',
            'description',
            'tags',
        ]);

        $this->uploadType = 'image';
        $this->disk = $this->defaultDisk();
        $this->visibility = 'public';
        $this->status = 'active';
        $this->company_id = auth()->user()?->defaultCompany()?->id;
        $this->updateMode = false;
        $this->resetErrorBag();
        $this->dispatch('media-gallery-reset-cropper');
    }

    public function closeImageModal(): void
    {
        $this->showImageModal = false;
        $this->resetInputFields();
    }

    public function closeDocumentModal(): void
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function closeFolderModal(): void
    {
        $this->showFolderModal = false;
        $this->folderName = '';
        $this->folderDescription = '';
        $this->createFolderParentId = null;
        $this->resetErrorBag();
    }

    public function storeFolder(): void
    {
        $validated = $this->validate([
            'folderName' => ['required', 'string', 'max:255'],
            'folderDescription' => ['nullable', 'string'],
        ]);

        MediaFolder::create([
            'company_id' => $this->company_id,
            'parent_id' => $this->createFolderParentId,
            'name' => trim($validated['folderName']),
            'description' => $this->blankToNull($validated['folderDescription'] ?? null),
        ]);

        $this->closeFolderModal();
        session()->flash('message', 'Folder created successfully.');
    }

    public function store(): void
    {
        $this->validate();

        $dimensions = $this->extractImageDimensions();
        $currentFolder = $this->currentFolder();

        $asset = MediaAsset::create([
            'company_id' => $this->company_id,
            'folder_id' => $currentFolder?->id,
            'name' => $this->resolvedName(),
            'title' => $this->blankToNull($this->title),
            'alt_text' => $this->blankToNull($this->alt_text),
            'description' => $this->blankToNull($this->description),
            'folder' => $currentFolder?->displayPath(),
            'disk' => $this->disk,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'tags' => $this->parsedTags(),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
        ]);

        $media = $asset->addMedia($this->mediaFile->getRealPath())
            ->usingName(pathinfo($this->mediaFile->getClientOriginalName(), PATHINFO_FILENAME))
            ->usingFileName($this->mediaFile->getClientOriginalName())
            ->withCustomProperties([
                'visibility' => $this->visibility,
                'uploaded_via' => 'mediagallery',
            ])
            ->toMediaCollection((string) config('mediagallery.collection_name', 'original'), $this->disk);

        $asset->syncFromMedia($media);

        session()->flash('message', 'Media asset uploaded successfully.');

        $this->closeActiveModal();
        $this->resetInputFields();
    }

    public function edit(int $id): void
    {
        $asset = MediaAsset::findOrFail($id);

        $this->assetId = $asset->id;
        $this->company_id = $asset->company_id;
        $this->name = (string) $asset->name;
        $this->title = (string) ($asset->title ?? '');
        $this->alt_text = (string) ($asset->alt_text ?? '');
        $this->description = (string) ($asset->description ?? '');
        $this->disk = (string) ($asset->disk ?: $this->defaultDisk());
        $this->visibility = (string) ($asset->visibility ?: 'public');
        $this->status = (string) ($asset->status ?: 'active');
        $this->tags = implode(', ', $asset->tagList());
        $this->mediaFile = null;
        $this->currentFolderId = $asset->folder_id;
        $this->uploadType = $asset->kind === 'image' ? 'image' : 'document';
        $this->updateMode = true;

        if ($this->uploadType === 'image') {
            $this->showModal = false;
            $this->showImageModal = true;
            $this->dispatch('media-gallery-image-modal-opened');
        } else {
            $this->showImageModal = false;
            $this->showModal = true;
        }
    }

    public function update(): void
    {
        $this->validate();

        $asset = MediaAsset::findOrFail($this->assetId);
        $currentFolder = $this->currentFolder();

        $dimensions = $this->mediaFile ? $this->extractImageDimensions() : [
            'width' => $asset->width,
            'height' => $asset->height,
        ];

        $asset->update([
            'company_id' => $this->company_id,
            'folder_id' => $currentFolder?->id,
            'name' => $this->resolvedName(),
            'title' => $this->blankToNull($this->title),
            'alt_text' => $this->blankToNull($this->alt_text),
            'description' => $this->blankToNull($this->description),
            'folder' => $currentFolder?->displayPath(),
            'disk' => $this->disk,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'tags' => $this->parsedTags(),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
        ]);

        if ($this->mediaFile) {
            $media = $asset->addMedia($this->mediaFile->getRealPath())
                ->usingName(pathinfo($this->mediaFile->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($this->mediaFile->getClientOriginalName())
                ->withCustomProperties([
                    'visibility' => $this->visibility,
                    'uploaded_via' => 'mediagallery',
                ])
                ->toMediaCollection((string) config('mediagallery.collection_name', 'original'), $this->disk);

            $asset->syncFromMedia($media);
        }

        session()->flash('message', 'Media asset updated successfully.');

        $this->closeActiveModal();
        $this->resetInputFields();
    }

    public function delete(): void
    {
        $asset = MediaAsset::findOrFail($this->deleteId);
        $asset->delete();

        $this->cancelDelete();
        session()->flash('message', 'Media asset archived successfully.');
    }

    public function render()
    {
        $currentFolder = $this->currentFolder();

        $folderQuery = MediaFolder::query()
            ->withCount(['children', 'assets'])
            ->when($currentFolder, function ($query) use ($currentFolder) {
                $query->where('parent_id', $currentFolder->id);
            }, function ($query) {
                $query->whereNull('parent_id');
            })
            ->when($this->search !== '', function ($builder) {
                $builder->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name');

        $folders = $folderQuery->get();

        $query = MediaAsset::query()
            ->withCount('links')
            ->with(['media', 'mediaFolder'])
            ->when($currentFolder, function ($query) use ($currentFolder) {
                $query->where('folder_id', $currentFolder->id);
            }, function ($query) {
                $query->whereNull('folder_id');
            })
            ->when($this->search !== '', function ($builder) {
                $builder->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('title', 'like', '%'.$this->search.'%')
                        ->orWhere('mime_type', 'like', '%'.$this->search.'%')
                        ->orWhere('folder', 'like', '%'.$this->search.'%');
                });
            });

        foreach ($this->filters as $field => $value) {
            if ($value !== '' && $value !== null) {
                $query->where($field, $value);
            }
        }

        $assets = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => MediaAsset::query()->count(),
            'active' => MediaAsset::query()->where('status', 'active')->count(),
            'images' => MediaAsset::query()->where('kind', 'image')->count(),
            'linked' => MediaAsset::query()->has('links')->count(),
        ];

        return view('mediagallery::livewire.media-assets.index', [
            'assets' => $assets,
            'folders' => $folders,
            'stats' => $stats,
            'allowedDisks' => $this->availableDisks(),
            'currentFolder' => $currentFolder,
            'createFolderParent' => $this->createFolderParent(),
            'breadcrumbs' => $currentFolder?->breadcrumbTrail() ?? collect(),
            'folderTreeItems' => $this->folderTreeItems(),
        ]);
    }

    private function availableDisks(): array
    {
        $configured = array_keys((array) config('filesystems.disks', []));
        $defaultDisk = $this->defaultDisk();

        if ($defaultDisk !== '' && ! in_array($defaultDisk, $configured, true)) {
            $configured[] = $defaultDisk;
        }

        return array_values(array_unique(array_filter($configured, fn ($disk) => is_string($disk) && $disk !== '')));
    }

    private function defaultDisk(): string
    {
        return (string) config('filesystems.default', config('mediagallery.default_disk', 'local'));
    }

    private function currentFolder(): ?MediaFolder
    {
        if (! $this->currentFolderId) {
            return null;
        }

        return MediaFolder::query()
            ->with('parent')
            ->find($this->currentFolderId);
    }

    private function createFolderParent(): ?MediaFolder
    {
        if (! $this->createFolderParentId) {
            return null;
        }

        return MediaFolder::query()->find($this->createFolderParentId);
    }

    private function folderTreeItems(): array
    {
        $folders = MediaFolder::query()
            ->select(['id', 'name', 'parent_id', 'path'])
            ->orderBy('name')
            ->get();

        return $this->flattenFolders(
            foldersByParent: $folders->groupBy(fn (MediaFolder $folder) => $folder->parent_id ?: 'root'),
            parentKey: 'root',
            depth: 0
        );
    }

    private function flattenFolders(Collection $foldersByParent, string|int $parentKey, int $depth): array
    {
        $items = [];

        foreach ($foldersByParent->get($parentKey, collect()) as $folder) {
            $items[] = [
                'id' => $folder->id,
                'name' => $folder->name,
                'path' => $folder->path,
                'depth' => $depth,
                'is_current' => (int) $this->currentFolderId === (int) $folder->id,
            ];

            $items = array_merge(
                $items,
                $this->flattenFolders($foldersByParent, (int) $folder->id, $depth + 1)
            );
        }

        return $items;
    }

    private function closeActiveModal(): void
    {
        if ($this->uploadType === 'image') {
            $this->showImageModal = false;

            return;
        }

        $this->showModal = false;
    }

    private function parsedTags(): array
    {
        return collect(explode(',', $this->tags))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    private function resolvedName(): string
    {
        $name = trim($this->name);

        if ($name !== '') {
            return $name;
        }

        if ($this->title !== '') {
            return trim($this->title);
        }

        return pathinfo($this->mediaFile?->getClientOriginalName() ?? ('asset-'.$this->assetId), PATHINFO_FILENAME);
    }

    private function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function extractImageDimensions(): array
    {
        $path = $this->mediaFile?->getRealPath();

        if (! $path || ! is_file($path)) {
            return ['width' => null, 'height' => null];
        }

        $imageSize = @getimagesize($path);

        if (! is_array($imageSize)) {
            return ['width' => null, 'height' => null];
        }

        return [
            'width' => $imageSize[0] ?? null,
            'height' => $imageSize[1] ?? null,
        ];
    }
}
