<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SupportDesk\Models\SupportTicketAction;
use Modules\SupportDesk\Models\SupportTicketDefect;
use Modules\SupportDesk\Models\SupportTicketSymptom;
use Spatie\Translatable\HasTranslations;

class ItemCategory extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['code', 'name', 'parent_id'];

    public $translatable = ['name'];

    public function getFilterableAttribute($value): array
    {
        return [
            'parent_id' => [
                'operator' => '=',
                'type' => 'select',
                'options' => self::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ],
        ];
    }

    public function parent()
    {
        return $this->belongsTo(ItemCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ItemCategory::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
