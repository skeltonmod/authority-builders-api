<?php

namespace Deyji\Manage\Contracts;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    public function roles(): BelongsToMany;

    public static function findByName(string $name, $guardName): self;

    public static function findById(int $id, $guardName): self;
    
    public static function findOrCreate(string $name, $guardName): self;
}