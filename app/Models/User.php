<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'id',
        'uuid',
        'full_name',
        'email',
        'password',
        'ic_number',
        'position',
        'race',
        'nationality',
        'gender',
        'is_active',
        'login_method',
        'is_change_password',
        'role_guid',
        'profile_picture',
        'password_changed_at',
        'last_login_at',
    ];

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'user_organizations', 'user_guid', 'org_guid');
    }

    public function isPasswordExpired()
    {
        // if (!$this->password_changed_at) {
        //     return true; // Force users to change password if it's never been updated
        // }

        return Carbon::parse($this->password_changed_at)->addDays(90)->isPast();
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_guid'); // Assuming role_guid is the foreign key
    }

   

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Use 'uuid' for route model binding
    public function getRouteKeyName()
    {
        return 'uuid';
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
}
