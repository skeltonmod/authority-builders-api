<?php

namespace Deyji\Manage\Classes;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Collection;

use Deyji\Manage\Contracts\Permission;
use Deyji\Manage\Contracts\Role;


class PermissionRegistrar{
    protected $cache;
    protected $cacheManager;
    protected $permissionClass;
    protected $roleClass;
    protected $permissions;
    public static $pivot_role;
    public static $pivotPermission;
    public static $cacheExpirationTime;
    public static $teams;
    public static $teamsKey;
    protected $teamId = null;
    public static $cacheKey;
    private $cachedRoles = [];

    public function __construct(CacheManager $cacheManager)
    {
        $this->permissionClass = config('privileges.models.permission');
        $this->roleClass = config('privileges.models.role');

        $this->cacheManager = $cacheManager;
        $this->initCache();
    }

    protected function initCache(){
    	self::$cacheExpirationTime = config('privileges.cache.expiration_time') ?: \DateInterval::createFromDateString('24 hours');

        self::$teams = config('privileges.teams', false);
        self::$teamsKey = config('privileges.column_names.team_foreign_key');

        self::$cacheKey = config('privileges.cache.key');

        self::$pivot_role = config('privileges.column_names.role_pivot_key') ?: 'role_id';
        self::$pivotPermission = config('privileges.column_names.permission_pivot_key') ?: 'permission_id';

        $this->cache = $this->getCacheStoreFromConfig();
    }

    protected function getCacheStoreFromConfig()
    {
        // the 'default' fallback here is from the permission.php config file,
        // where 'default' means to use config(cache.default)
        $cacheDriver = config('privileges.cache.store', 'default');

        // when 'default' is specified, no action is required since we already have the default instance
        if ($cacheDriver === 'default') {
            return $this->cacheManager->store();
        }

        // if an undefined cache store is specified, fallback to 'array' which is Laravel's closest equiv to 'none'
        if (! \array_key_exists($cacheDriver, config('cache.stores'))) {
            $cacheDriver = 'array';
        }

        return $this->cacheManager->store($cacheDriver);
    }


    public function setPermissionsTeamId(?int $id)
    {
        $this->teamId = $id;
    }

    public function getPermissionsTeamId(): ?int
    {
        return $this->teamId;
    }

    public function registerPermissions(): bool
    {
        app(Gate::class)->before(function (Authorizable $user, string $ability) {
            if (method_exists($user, 'checkPermissionTo')) {
                return $user->checkPermissionTo($ability) ?: null;
            }
        });

        return true;
    }


    // Flush the cache
    public function forgetCachedPermissions()
    {
        $this->permissions = null;

        return $this->cache->forget(self::$cacheKey);
    }

    public function clearClassPermissions()
    {
        $this->permissions = null;
    }

    private function loadPermissions()
    {
        if ($this->permissions !== null) {
            return;
        }

        $this->permissions = $this->cache->remember(self::$cacheKey, self::$cacheExpirationTime, function () {
            // make the cache smaller using an array with only required fields
            return $this->getPermissionClass()->select('id', 'id as i', 'name as n', 'guard_name as g')
                ->with('roles:id,id as i,name as n,guard_name as g')->get()
                ->map(function ($permission) {
                    return $permission->only('i', 'n', 'g') +
                        ['r' => $permission->roles->map->only('i', 'n', 'g')->all()];
                })->all();
        });

        if (is_array($this->permissions)) {
            $this->permissions = $this->getPermissionClass()::hydrate(
                collect($this->permissions)->map(function ($item) {
                    return ['id' => $item['i'] ?? $item['id'], 'name' => $item['n'] ?? $item['name'], 'guard_name' => $item['g'] ?? $item['guard_name']];
                })->all()
            )
            ->each(function ($permission, $i) {
                $roles = Collection::make($this->permissions[$i]['r'] ?? $this->permissions[$i]['roles'] ?? [])
                        ->map(function ($item) {
                            return $this->getHydratedRole($item);
                        });

                $permission->setRelation('roles', $roles);
            });

            $this->cachedRoles = [];
        }
    }

    public function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        $this->loadPermissions();

        $method = $onlyOne ? 'first' : 'filter';

        $permissions = $this->permissions->$method(static function ($permission) use ($params) {
            foreach ($params as $attr => $value) {
                if ($permission->getAttribute($attr) != $value) {
                    return false;
                }
            }

            return true;
        });

        if ($onlyOne) {
            $permissions = new Collection($permissions ? [$permissions] : []);
        }

        return $permissions;
    }


    public function getPermissionClass(): Permission
    {
        return app($this->permissionClass);
    }

    public function setPermissionClass($permissionClass)
    {
        $this->permissionClass = $permissionClass;

        return $this;
    }

    public function getRoleClass(): Role
    {
        return app($this->roleClass);
    }


    public function getCacheStore(): \Illuminate\Contracts\Cache\Store
    {
        return $this->cache->getStore();
    }

    private function getHydratedRole(array $item)
    {
        $roleId = $item['i'] ?? $item['id'];

        if (isset($this->cachedRoles[$roleId])) {
            return $this->cachedRoles[$roleId];
        }

        $roleClass = $this->getRoleClass();
        $roleInstance = new $roleClass();

        return $this->cachedRoles[$roleId] = $roleInstance->newFromBuilder([
            'id' => $roleId,
            'name' => $item['n'] ?? $item['name'],
            'guard_name' => $item['g'] ?? $item['guard_name'],
        ]);
    }
}