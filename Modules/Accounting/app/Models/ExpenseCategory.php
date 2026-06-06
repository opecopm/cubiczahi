<?php

namespace Modules\Accounting\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory, TrackUser;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'parent_id'];

    public $translatable = ['name'];

    // protected static function newFactory(): ExpenseCategoryFactory
    // {
    //     // return ExpenseCategoryFactory::new();
    // }

    public function parent()
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }
}
