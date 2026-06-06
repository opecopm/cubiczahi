<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'cms_testimonials';

    // Spatie translatable fields
    protected $translatable = [
        'name',
        'designation',
        'company',
        'website',
        'location',
        'phone',
        'message',
        'about',
    ];

    protected $fillable = [
        'email',
        'image',
        'video_url',
        'video_path',
        'rating',
        'featured',
        'sort_order',
        'status',
        'name',
        'designation',
        'company',
        'website',
        'location',
        'phone',
        'message',
        'about',
    ];

    // You can now use $testimonial->getTranslation('name', 'en')
    // and $testimonial->setTranslation('name', 'ar', '...') with Spatie
}
