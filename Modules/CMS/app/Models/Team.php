<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Team extends Model
{
    use HasTranslations;

    protected $table = 'cms_teams';

    /**
     * Translatable fields
     */
    protected $translatable = [
        'name',
        'designation',
        'phone',
        'bio',
        'message',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
    ];

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'email',
        'photo',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'status',
        'sort_order',
        'name',
        'designation',
        'phone',
        'bio',
        'message',
    ];



    /**
     * Accessor for status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Scope: Active teams only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
