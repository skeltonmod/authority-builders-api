<?php

namespace Deyji\Manage\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role{
    /**
     * A role may be given various permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany;
    public static function findByName(string $name, $guardName): self;
    public static function findById(int $id, $guardName): self;
    public static function findOrCreate(string $name, $guardName): self;
    public function hasPermissionTo($permission): bool;
    public function getModelForGuard(string $guard);
}