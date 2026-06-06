<?php

namespace Modules\Global\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GeneralDocument extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, TrackUser;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'name',
        'type',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuing_country',
        'issuing_entity',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the parent documentable model (customer, vendor, employee, etc.).
     */
    public function documentable()
    {
        return $this->morphTo();
    }
}
