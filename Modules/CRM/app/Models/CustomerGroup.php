<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(CustomerGroup::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CustomerGroup::class, 'parent_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_group_id');
    }
}
