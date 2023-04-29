<?php
namespace Deyji\Manage\Models\Privilege;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;

use Deyji\Manage\Contracts\Permission as PermissionContract;
use Deyji\Manage\Classes\Guard;
use Deyji\Manage\Classes\PermissionRegistrar;

use Deyji\Manage\Traits\HasPermission;
use Deyji\Manage\Traits\HasRoles;
use Exception;


class Permission extends Model implements PermissionContract{
	use HasRoles;

	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);
	}

	public function getTable()
    {
        return config('privileges.table_names.permissions', parent::getTable());
    }


    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $permission = static::getPermission(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']]);

        if ($permission) {
            throw new Exception("Permission already Exists!");
        }

        return static::query()->create($attributes);
    }


    public function roles(): BelongsToMany { 
    	return $this->belongsToMany(
            config('privileges.models.role'),
            config('privileges.table_names.role_has_permissions'),
            PermissionRegistrar::$pivotPermission,
            PermissionRegistrar::$pivot_role
        );

    }

     public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('privileges.table_names.model_has_permissions'),
            PermissionRegistrar::$pivotPermission,
            config('privileges.column_names.model_morph_key')
        );
    }

    public static function findByName(string $name, $guardName): PermissionContract { 
    	$guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (! $permission) {
            throw new Exception("Permission does not Exists!");
        }

        return $permission;
    }

    public static function findById(int $id, $guardName): PermissionContract { 
    	$guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['id' => $id, 'guard_name' => $guardName]);

        if (! $permission) {
            throw new Exception("Permission does not Exists!");
        }

        return $permission;

    }

    public static function findOrCreate(string $name, $guardName): PermissionContract { 
    	$guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);

        if (! $permission) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $permission;

    }

    protected static function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params, $onlyOne);
    }

    protected static function getPermission(array $params = []): ?PermissionContract
    {
        return static::getPermissions($params, true)->first();
    }
}