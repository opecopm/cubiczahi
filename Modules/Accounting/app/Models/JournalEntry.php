<?php

namespace Modules\Accounting\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Accounting\Database\Factories\JounralEntryFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory, TrackUser;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'transaction_date',
        'reference',
        'description',
        'total_debit',
        'total_credit',
        'created_by',
    ];

    // protected static function newFactory(): JounralEntryFactory
    // {
    //     // return JounralEntryFactory::new();
    // }
}
