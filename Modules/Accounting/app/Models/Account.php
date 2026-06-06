<?php

namespace Modules\Accounting\Models;

use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Accounting\Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, TrackUser;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'code',
        'name',
        'type',
        'description',
        'currency',
        'parent_id',
        'active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    const TYPE_SELECT = [
        'accounts_payable' => 'Accounts Payable',
        'accounts_receivable' => 'Accounts Receivable',
        'asset' => 'Asset',
        'cash' => 'Cash',
        'cost_of_goods_sold' => 'Cost Of Goods Sold',
        'equity' => 'Equity',
        'expense' => 'Expense',
        'income' => 'Income',
        'liability' => 'Liability',
        'other_current_asset' => 'Other Current Asset',
        'other_current_liability' => 'Other Current Liability',
        'other_liability' => 'Other Liability',
        'stock' => 'Stock',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
