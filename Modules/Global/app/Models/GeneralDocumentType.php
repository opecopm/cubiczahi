<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralDocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
