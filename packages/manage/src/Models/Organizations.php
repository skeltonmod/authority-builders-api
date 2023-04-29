<?php

namespace Deyji\Manage\Models;

use Deyji\Manage\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organizations extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name'
    ];

    public $timestamps = false;

    public function users(){
        return $this->belongsToMany(Users::class, 'organizations_users', 'organizations_id', 'users_id');
    }
}
