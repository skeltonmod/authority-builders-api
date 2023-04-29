<?php

namespace Deyji\Manage\Models\Privilege;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Deyji\Manage\Contracts\Role as RoleContract;
use Deyji\Manage\Contracts\Permission;

use Deyji\Manage\Models\Users;
use Deyji\Manage\Classes\Guard;
use Deyji\Manage\Classes\PermissionRegistrar;

use Deyji\Manage\Traits\HasPermissions;
use Exception;

class Role extends Model implements RoleContract{
    use HasPermissions;
	protected $guarded = ['id'];


	public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];
        if (PermissionRegistrar::$teams) {
            if (array_key_exists(PermissionRegistrar::$teamsKey, $attributes)) {
                $params[PermissionRegistrar::$teamsKey] = $attributes[PermissionRegistrar::$teamsKey];
            } else {
                $attributes[PermissionRegistrar::$teamsKey] = app(PermissionRegistrar::class)->getPermissionsTeamId();
            }
        }
        if (static::findByParam($params)) {
            throw new Exception("Role already Exist!");
        }

        return static::query()->create($attributes);
    }


    public function getTable()
    {
        return config('privileges.table_names.roles', parent::getTable());
    }

    public function permissions(): BelongsToMany { 
    	return $this->belongsToMany(
            config('privileges.models.permission'),
            config('privileges.table_names.role_has_permissions'),
            PermissionRegistrar::$pivot_role,
            PermissionRegistrar::$pivotPermission
        );

    }

    public function getModelForGuard(string $guard)
    {
        return collect(config('auth.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return;
                }

                return config("auth.providers.{$guard['provider']}.model");
            })->get($guard);
    }

    public function users()
    {
        return $this->morphedByMany(Users::class, 'model_has_roles');
    }

    public static function findByName(string $name, $guardName): RoleContract { 
    	$guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (!$role) {
            throw new Exception("Role does not Exist");
        }

        return $role;

    }

    public static function findById(int $id, $guardName): RoleContract { 
    	 $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['id' => $id, 'guard_name' => $guardName]);

        if (!$role) {
            throw new Exception("Role does not Exist");
        }

        return $role;
    }

    public static function findOrCreate(string $name, $guardName): RoleContract { 
    	$guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName] + (PermissionRegistrar::$teams ? [PermissionRegistrar::$teamsKey => app(PermissionRegistrar::class)->getPermissionsTeamId()] : []));
        }

        return $role;

    }

    protected static function findByParam(array $params = [])
    {
        $query = static::when(PermissionRegistrar::$teams, function ($q) use ($params) {
            $q->where(function ($q) use ($params) {
                $q->whereNull(PermissionRegistrar::$teamsKey)
                    ->orWhere(PermissionRegistrar::$teamsKey, $params[PermissionRegistrar::$teamsKey] ?? app(PermissionRegistrar::class)->getPermissionsTeamId());
            });
        });
        unset($params[PermissionRegistrar::$teamsKey]);
        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public function hasPermissionTo($permission): bool { 
    	// if (config('permission.enable_wildcard_permission', false)) {
     //        return $this->hasWildcardPermission($permission, $this->getDefaultGuardName());
     //    }

        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw new Exception("Guard Mismatch");
        }

        return $this->permissions->contains('id', $permission->id);

    }

   

}