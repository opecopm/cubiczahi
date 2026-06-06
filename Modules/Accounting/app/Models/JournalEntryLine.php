<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Accounting\Database\Factories\JounralEntryLineFactory;

class JournalEntryLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'journal_entry_id',
        'account_id', // Expense Account ID
        'debit',
        'credit',
        'description',
    ];

    // protected static function newFactory(): JounralEntryLineFactory
    // {
    //     // return JounralEntryLineFactory::new();
    // }
}
