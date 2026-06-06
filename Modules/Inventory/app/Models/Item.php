<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use App\Traits\HasCustomFields;
use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Global\Models\ReferenceSchema;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Item extends Model implements HasMedia
{
    use HasCustomFields;
    use HasFactory, HasTranslations, InteractsWithMedia, TrackUser;

    const TYPE_SELECT = [
        'product' => 'Product',
        'service' => 'Service',
    ];

    const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    /**
     * Accessor for readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_SELECT[$this->status] ?? ucfirst($this->status ?? '');
    }

    public function getFilterableAttribute($value): array
    {
        return [
            'type' => [
                'operator' => '=',
                'type' => 'select',
                'options' => self::TYPE_SELECT,
            ],
            'category_id' => [
                'operator' => '=',
                'type' => 'select',
                'options' => ItemCategory::orderBy('name')->pluck('name', 'id')->toArray(),
            ],
            'brand_id' => [
                'operator' => '=',
                'type' => 'select',
                'options' => Brand::orderBy('name')->pluck('name', 'id')->toArray(),
            ],
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'options' => self::STATUS_SELECT,
            ],
        ];
    }

    protected $fillable = [
        'id',
        'reference',
        'slug',
        'type',
        'name',
        'short_description',
        'description',
        'unit_label',
        'icon_class',
        'category_id',
        'brand_id',
        'track_inventory',
        'is_serialized',
        'has_variants',
        'status',
        'created_by',
        'updated_by',
    ];

    public $translatable = ['name', 'description', 'short_description'];

    protected $casts = [
        'is_serialized' => 'boolean',
        'has_variants' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (blank($item->reference)) {
                $schemaType = $item->type === 'service' ? 'item_service' : 'item';
                $item->reference = ReferenceSchema::generate($schemaType);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class)->orderBy('display_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class)->where('is_primary', true)->orderBy('display_order');
    }

    public function price($type)
    {
        return ItemPrice::where('price_type', $type)->where('item_id', $this->id)->first();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('primary_photo')
            ->useFallbackUrl('/assets/img/no-photo.jpg') // Default image if none is uploaded
            ->singleFile();
        $this->addMediaCollection('manual')
            ->acceptsMimeTypes(['application/pdf'])
            ->singleFile();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // 1. Get the values (with field relation)
    public function customValues()
    {
        return $this->hasMany(ItemCustomValue::class);
    }

    public function prices()
    {
        return $this->hasMany(ItemPrice::class, 'item_id');
    }

    public function variants()
    {
        return $this->hasMany(ItemVariant::class)->orderBy('attribute_id')->orderBy('sort_order');
    }

    public function activeVariants()
    {
        return $this->hasMany(ItemVariant::class)
            ->where('status', 'active')
            ->orderBy('attribute_id')
            ->orderBy('sort_order');
    }
}
