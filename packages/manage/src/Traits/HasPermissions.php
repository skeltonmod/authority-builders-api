<?php

namespace Deyji\Manage\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

use Deyji\Manage\Classes\Guard;
use Deyji\Manage\Classes\PermissionRegistrar;
use Deyji\Manage\Contracts\Permission;
use Deyji\Manage\Contracts\Role;
use Exception;

trait HasPermissions{
	private $permissionClass;

    public static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->permissions()->detach();
        });
    }

    public function getPermissionClass()
    {
        if (! isset($this->permissionClass)) {
            $this->permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
        }

        return $this->permissionClass;
    }


    public function permissions(): BelongsToMany
    {
        return $this->morphToMany(
            config('privileges.models.permission'),
            'model',
            config('privileges.table_names.model_has_permissions'),
            config('privileges.column_names.model_morph_key'),
            PermissionRegistrar::$pivotPermission
        )
        ->where(function ($q) {
            $q->when(PermissionRegistrar::$teams, function ($q) {
                $q->where(PermissionRegistrar::$teamsKey, app(PermissionRegistrar::class)->getPermissionsTeamId());
            });
        });
    }

    public function scopePermission(Builder $query, $permissions): Builder
    {
        $permissions = $this->convertToPermissionModels($permissions);

        $rolesWithPermissions = array_unique(array_reduce($permissions, function ($result, $permission) {
            return array_merge($result, $permission->roles->all());
        }, []));

        return $query->where(function (Builder $query) use ($permissions, $rolesWithPermissions) {
            $query->whereHas('permissions', function (Builder $subQuery) use ($permissions) {
                $subQuery->whereIn(config('privileges.table_names.permissions').'.id', \array_column($permissions, 'id'));
            });
            if (count($rolesWithPermissions) > 0) {
                $query->orWhereHas('roles', function (Builder $subQuery) use ($rolesWithPermissions) {
                    $subQuery->whereIn(config('privileges.table_names.roles').'.id', \array_column($rolesWithPermissions, 'id'));
                });
            }
        });
    }

    protected function convertToPermissionModels($permissions): array
    {
        if ($permissions instanceof Collection) {
            $permissions = $permissions->all();
        }

        $permissions = is_array($permissions) ? $permissions : [$permissions];

        return array_map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission;
            }

            return $this->getPermissionClass()->findByName($permission, $this->getDefaultGuardName());
        }, $permissions);
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if (config('permission.enable_wildcard_permission', false)) {
            return $this->hasWildcardPermission($permission, $guardName);
        }

        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        }

        if (! $permission instanceof Permission) {
            throw new Exception("Permission doesn't Exist");
        }

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    public function hasUncachedPermissionTo($permission, $guardName = null): bool
    {
        return $this->hasPermissionTo($permission, $guardName);
    }


    public function checkPermissionTo($permission, $guardName = null): bool
    {
        try {
            return $this->hasPermissionTo($permission, $guardName);
        } catch (Exception $e) {
            return false;
        }
    }


    public function hasAnyPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if ($this->checkPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if (! $this->hasPermissionTo($permission)) {
                return false;
            }
        }

        return true;
    }


    protected function hasPermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }



	public function hasDirectPermission($permission): bool
    {
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }

        if (! $permission instanceof Permission) {
            throw new Exception("Permission does not Exist");
        }

        return $this->permissions->contains('id', $permission->id);
    }

    public function getPermissionsViaRoles(): Collection
    {
        return $this->loadMissing('roles', 'roles.permissions')
            ->roles->flatMap(function ($role) {
                return $role->permissions;
            })->sort()->values();
    }

    public function getAllPermissions(): Collection
    {
        /** @var Collection $permissions */
        $permissions = $this->permissions;

        if ($this->roles) {
            $permissions = $permissions->merge($this->getPermissionsViaRoles());
        }

        return $permissions->sort()->values();
    }


    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->each(function ($permission) {
                $this->ensureModelSharesGuard($permission);
            })
            ->map(function ($permission) {
                return ['id' => $permission->id, 'values' => PermissionRegistrar::$teams && ! is_a($this, Role::class) ?
                    [PermissionRegistrar::$teamsKey => app(PermissionRegistrar::class)->getPermissionsTeamId()] : [],
                ];
            })
            ->pluck('values', 'id')->toArray();

        $model = $this->getModel();

        if ($model->exists) {
            if (PermissionRegistrar::$teams && ! is_a($this, Role::class)) {
                $this->permissions()->wherePivot(PermissionRegistrar::$teamsKey, app(PermissionRegistrar::class)->getPermissionsTeamId())->sync($permissions, false);
            } else {
                $this->permissions()->sync($permissions, false);
            }
            $model->load('permissions');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($permissions, $model) {
                    if ($model->getKey() != $object->getKey()) {
                        return;
                    }
                    $model->permissions()->sync($permissions, false);
                    $model->load('permissions');
                }
            );
        }

        if (is_a($this, get_class(app(PermissionRegistrar::class)->getRoleClass()))) {
            $this->forgetCachedPermissions();
        }

        return $this;
    }


    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($this->getStoredPermission($permission));

        if (is_a($this, get_class(app(PermissionRegistrar::class)->getRoleClass()))) {
            $this->forgetCachedPermissions();
        }

        $this->load('permissions');

        return $this;
    }

    public function getPermissionNames(): Collection
    {
        return $this->permissions->pluck('name');
    }


    protected function getStoredPermission($permissions)
    {
        $permissionClass = $this->getPermissionClass();

        if (is_numeric($permissions)) {
            return $permissionClass->findById($permissions, $this->getDefaultGuardName());
        }

        if (is_string($permissions)) {
            return $permissionClass->findByName($permissions, $this->getDefaultGuardName());
        }

        if (is_array($permissions)) {
            return $permissionClass
                ->whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }

        return $permissions;
    }

    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (! $this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw new Exception("Guard Mismatch");
        }
    }

    protected function getGuardNames(): Collection
    {
        return Guard::getNames($this);
    }

    protected function getDefaultGuardName(): string
    {
        return Guard::getDefaultName($this);
    }

    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function hasAllDirectPermissions(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if (! $this->hasDirectPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function hasAnyDirectPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if ($this->hasDirectPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}