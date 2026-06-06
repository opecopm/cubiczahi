<?php

namespace Modules\Business\Models;

use App\Models\User;
use App\Traits\TrackUser;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, TrackUser;

    /**
     * The attributes that are mass assignable.
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public const TYPE_SELECT = [
        'Internal' => 'Internal',
        'Project' => 'Project',
    ];

    public const STATUS_SELECT = [
        'published' => 'Published',
        'draft' => 'Draft',
        'archived' => 'Archived',
    ];

    protected $fillable = [
        'name',
        'type',
        'status',
        'parent_id',
        'hod_id',
        'created_by',
        'updated_by',
    ];

    public $orderable = [
        'id',
        'name',
        'type',
        'status',
        'parent.name',
        'created_by',
        'updated_by',
    ];

    public function getFilterableAttribute($value): array
    {
        return [
            'id' => [
                'operator' => '=',
                'type' => 'text',
                'label' => 'ID',
            ],
            'name' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Name',
            ],
            'type' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Type',
                'options' => self::TYPE_SELECT,
            ],
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Status',
                'options' => self::STATUS_SELECT,
            ],
            'parent_id' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Parent',
                'options' => self::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ],
            'created_by' => [
                'operator' => '=',
                'type' => 'text',
                'label' => 'Created By',
            ],
            'updated_by' => [
                'operator' => '=',
                'type' => 'text',
                'label' => 'Updated By',
            ],
        ];
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getTypeLabelAttribute($value)
    {
        return static::TYPE_SELECT[$this->type] ?? null;
    }

    public function getStatusLabelAttribute($value)
    {
        return static::STATUS_SELECT[$this->status] ?? null;
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }

    public function hodUser()
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }
}
