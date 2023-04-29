<?php

namespace Deyji\Manage\Models;

use Deyji\Manage\PermissionRegistrar;
use Deyji\Manage\Models\Privilege\Role;
use Deyji\Manage\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Deyji\Manage\Models\Maps;

class Users extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function map(){
        return $this->hasOne(Maps::class);
    }

    public function organization(){
        return $this->belongsToMany(Organizations::class, 'organizations_users', 'users_id', 'organizations_id');
    }
}
