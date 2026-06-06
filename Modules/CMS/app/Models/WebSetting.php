<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class WebSetting extends Model implements HasMedia
{
    use HasFactory, HasTranslations,InteractsWithMedia;

    protected $table = 'cms_web_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['key', 'value'];

    public $translatable = ['value']; // this makes `value` translatable
}
