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

class MediaFolder extends Model
{
    use HasFactory, SoftDeletes, TrackUser;

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'slug',
        'path',
        'description',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $folder): void {
            $folder->slug = Str::slug((string) $folder->name);
            $folder->path = $folder->buildPath();
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(MediaAsset::class, 'folder_id');
    }

    public function breadcrumbTrail(): Collection
    {
        $trail = collect();
        $folder = $this;
        $seen = 0;

        while ($folder && $seen < 50) {
            $trail->prepend($folder);
            $folder = $folder->parent;
            $seen++;
        }

        return $trail->values();
    }

    public function displayPath(): string
    {
        return (string) ($this->path ?: $this->name);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    private function buildPath(): string
    {
        $segment = trim((string) $this->name, '/');

        if (! $this->parent_id) {
            return $segment;
        }

        $parent = $this->relationLoaded('parent') ? $this->parent : $this->parent()->first();

        if (! $parent) {
            return $segment;
        }

        return trim($parent->displayPath().'/'.$segment, '/');
    }
}
