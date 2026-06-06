<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PM\Models\Project;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, TrackUser;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id',
        'amount',
        'vendor_id',
        'project_id',
        'description',
        'expense_date',
        'status',
        'reference_number',
        'created_by',
        'updated_by',
    ];

    const STATUS_SELECT = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'paid' => 'Paid',
    ];

    /*public static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            $expense->reference = generateSerialNumber('expense');
        });
    }*/

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function Project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
