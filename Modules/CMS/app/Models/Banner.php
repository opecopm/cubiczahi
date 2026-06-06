<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasTranslations;

    protected $table = 'cms_banners';

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public $translatable = ['name'];

    protected $casts = [
        'status' => 'boolean',
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // Accessor for readable status
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    // Relation: Banner has many items
    public function items()
    {
        return $this->hasMany(BannerItem::class);
    }

    // 🔹 Boot method for auto slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($banner) {
            $banner->slug = static::generateUniqueSlug($banner->name);
        });

        static::updating(function ($banner) {
            if ($banner->isDirty('name')) {
                $banner->slug = static::generateUniqueSlug($banner->name, $banner->id);
            }
        });
    }

    // 🔹 Unique slug generator
    protected static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}
