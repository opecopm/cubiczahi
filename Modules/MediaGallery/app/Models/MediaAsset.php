<?php

namespace Modules\MediaGallery\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Business\Models\Company;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaAsset extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes, TrackUser;

    protected $fillable = [
        'uuid',
        'company_id',
        'folder_id',
        'name',
        'title',
        'alt_text',
        'description',
        'folder',
        'disk',
        'visibility',
        'mime_type',
        'extension',
        'size',
        'kind',
        'width',
        'height',
        'duration_seconds',
        'checksum',
        'status',
        'tags',
        'custom_properties',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'folder_id' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration_seconds' => 'integer',
        'tags' => 'array',
        'custom_properties' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            $asset->uuid ??= (string) Str::uuid();
            $asset->disk ??= (string) config('filesystems.default', config('mediagallery.default_disk', 'local'));
            $asset->visibility ??= (string) config('mediagallery.default_visibility', 'public');
            $asset->name = trim((string) ($asset->name ?: $asset->title ?: $asset->uuid));
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection((string) config('mediagallery.collection_name', 'original'))
            ->singleFile();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function mediaFolder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(MediaLink::class, 'media_asset_id');
    }

    public function primaryMedia(): ?Media
    {
        return $this->getFirstMedia((string) config('mediagallery.collection_name', 'original'));
    }

    public function primaryUrl(): string
    {
        return (string) $this->getFirstMediaUrl((string) config('mediagallery.collection_name', 'original'));
    }

    public function showUrl(): string
    {
        return route('admin.mediagallery.media-assets.show', $this);
    }

    public function previewUrl(): string
    {
        return route('admin.mediagallery.media-assets.preview', $this);
    }

    public function downloadUrl(): string
    {
        return route('admin.mediagallery.media-assets.download', $this);
    }

    public function folderPath(): string
    {
        return (string) ($this->mediaFolder?->displayPath() ?: $this->folder ?: 'Root');
    }

    public function tagList(): array
    {
        return Collection::make($this->tags)
            ->filter(fn ($tag) => filled($tag))
            ->map(fn ($tag) => trim((string) $tag))
            ->values()
            ->all();
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with((string) $this->mime_type, 'video/');
    }

    public function isAudio(): bool
    {
        return str_starts_with((string) $this->mime_type, 'audio/');
    }

    public function isPdf(): bool
    {
        return strtolower((string) $this->mime_type) === 'application/pdf'
            || strtolower((string) $this->extension) === 'pdf';
    }

    public function canPreviewInline(): bool
    {
        return $this->isImage() || $this->isVideo() || $this->isAudio() || $this->isPdf();
    }

    public function displayTitle(): string
    {
        return (string) ($this->title ?: $this->name ?: ('Asset #'.$this->id));
    }

    public function formattedSize(): string
    {
        $size = (int) $this->size;

        if ($size < 1024) {
            return $size.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $size / 1024;
        $unitIndex = 0;

        while ($value >= 1024 && $unitIndex < count($units) - 1) {
            $value /= 1024;
            $unitIndex++;
        }

        return number_format($value, 2).' '.$units[$unitIndex];
    }

    public function attachTo(Model $model, ?string $usage = null, ?string $collectionName = null, array $context = []): MediaLink
    {
        return $this->links()->updateOrCreate(
            [
                'linkable_type' => $model->getMorphClass(),
                'linkable_id' => (string) $model->getKey(),
                'usage' => $usage,
                'collection_name' => $collectionName,
            ],
            [
                'context' => $context,
            ]
        );
    }

    public function syncFromMedia(?Media $media = null): self
    {
        $media ??= $this->primaryMedia();

        if (! $media) {
            return $this;
        }

        $this->forceFill([
            'disk' => $media->disk ?: $this->disk,
            'mime_type' => $media->mime_type,
            'extension' => pathinfo((string) $media->file_name, PATHINFO_EXTENSION) ?: null,
            'size' => (int) $media->size,
            'name' => $this->name ?: pathinfo((string) $media->file_name, PATHINFO_FILENAME),
            'title' => $this->title ?: $media->name,
            'kind' => $this->determineKind($media->mime_type, (string) $media->file_name),
        ])->save();

        return $this;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    private function determineKind(?string $mimeType, string $fileName): string
    {
        $mimeType = strtolower((string) $mimeType);
        $extension = strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION));

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if ($mimeType === 'application/pdf' || $extension === 'pdf') {
            return 'document';
        }

        return 'file';
    }
}
