<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    public $translatable = ['name'];

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'menus_menu_items');
    }
}
