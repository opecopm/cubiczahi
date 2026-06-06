<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Accounting\Database\Factories\AccountPayableFactory;

class AccountPayable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): AccountPayableFactory
    // {
    //     // return AccountPayableFactory::new();
    // }
}
