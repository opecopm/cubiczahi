<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Business\Models\Company;
use Modules\Business\Models\Location;
use Modules\CRM\Models\Customer;
use Modules\System\Models\Menu;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'userable_type',
        'userable_id',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone_code',
        'phone',
        'password',
        'menu_id',
        'location_id',
        'default_project_id',
        'language',
        'status',
        'otp',
        'otp_expires_at',
        'mfa_enabled',
    ];

    public function getFilterableAttribute(): array
    {
        return [
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'options' => self::STATUS_SELECT,
            ],
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at'    => 'datetime',
            'password'          => 'hashed',
            'mfa_enabled'       => 'boolean',
        ];
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function ensureCustomerProfile(): Customer
    {
        if ($this->userable instanceof Customer) {
            return $this->userable;
        }

        $customer = Customer::updateOrCreate(
            [
                'email' => $this->email,
                'phone_code' => $this->phone_code,
                'phone' => $this->phone,
            ],
            [
                'name' => $this->name,
            ]
        );

        $this->userable()->associate($customer);
        $this->save();
        $this->setRelation('userable', $customer);

        return $customer;
    }

    public function initials(): string
    {
        $first = trim((string) ($this->first_name ?? ''));
        $last = trim((string) ($this->last_name ?? ''));

        if ($first !== '' && $last !== '') {
            return mb_strtoupper(mb_substr($first, 0, 1).mb_substr($last, 0, 1));
        }

        if ($first !== '') {
            return mb_strtoupper(mb_substr($first, 0, 2));
        }

        $email = trim((string) ($this->email ?? ''));
        if ($email !== '') {
            return mb_strtoupper(mb_substr($email, 0, 2));
        }

        return 'U';
    }

    public function getInitialsAttribute(): string
    {
        return $this->initials();
    }

    public function getNameAttribute(): string
    {
        $fullName = trim(trim((string) ($this->first_name ?? '')).' '.trim((string) ($this->last_name ?? '')));
        if ($fullName !== '') {
            return $fullName;
        }

        $email = trim((string) ($this->email ?? ''));
        if ($email !== '') {
            return $email;
        }

        return 'User';
    }

    // public function isCustomer(): bool
    // {
    //     return $this->userable_type === Customer::class;
    // }

    // public function isVendor(): bool
    // {
    //     return $this->userable_type === Vendor::class;
    // }

    public function assignedProjects()
    {
        return collect();
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_companies')
            ->withPivot('is_default');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'user_locations');
    }

    public function defaultCompany()
    {
        return $this->companies()->wherePivot('is_default', true)->first();
    }

    public function managedLocations()
    {
        return $this->hasMany(Location::class, 'manager_id');
    }

    public function can($abilities, $arguments = [])
    {
        // Attempt direct permission check for simple string abilities
        if (is_string($abilities) && empty($arguments)) {
            try {
                if ($this->hasPermissionTo($abilities)) {
                    return true;
                }
            } catch (PermissionDoesNotExist) {
                // Permission doesn't exist in DB, let Gate handle it (might be a Policy)
            }
        }

        return parent::can($abilities, $arguments);
    }
}
