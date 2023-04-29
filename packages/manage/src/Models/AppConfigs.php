<?php

namespace Deyji\Manage\Models;

use Illuminate\Database\Eloquent\Model;

class AppConfigs extends Model{

    protected $fillable = [
        "file_path"
    ];

    public $timestamps = false;
}