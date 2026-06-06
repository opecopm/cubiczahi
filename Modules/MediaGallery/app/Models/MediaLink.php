<?php

namespace Modules\MediaGallery\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaLink extends Model
{
    use HasFactory, TrackUser;

    protected $fillable = [
        'media_asset_id',
        'linkable_type',
        'linkable_id',
        'usage',
        'collection_name',
        'sort_order',
        'is_primary',
        'context',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'media_asset_id' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'context' => 'array',
    ];

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForUsage(Builder $query, string $usage): Builder
    {
        return $query->where('usage', $usage);
    }

    public function linkableTypeLabel(): string
    {
        return class_basename((string) $this->linkable_type);
    }

    public function linkableDisplayName(): string
    {
        $linkable = $this->linkable;

        if (! $linkable) {
            return $this->linkableTypeLabel().' #'.$this->linkable_id;
        }

        foreach (['title', 'name', 'subject', 'reference', 'code', 'full_name'] as $attribute) {
            $value = data_get($linkable, $attribute);
            if (filled($value)) {
                return (string) $value;
            }
        }

        return class_basename($linkable).' #'.$linkable->getKey();
    }
}
