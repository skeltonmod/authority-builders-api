<?php

namespace Deyji\Manage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Deyji\Manage\Models\Users;

class Maps extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'status',
        'canonical_name',
        'user_id'
    ];
    public $timestamps = false;
    public function user(): BelongsTo{
        return $this->belongsTo(Users::class);
    }
}
