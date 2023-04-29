<?php

namespace Deyji\Manage\Classes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Guard{
	public static function getNames($model): Collection
	    {
	        $class = is_object($model) ? get_class($model) : $model;

	        if (is_object($model)) {
	            if (\method_exists($model, 'guardName')) {
	                $guardName = $model->guardName();
	            } else {
	                $guardName = $model->guard_name ?? null;
	            }
	        }

	        if (! isset($guardName)) {
	            $guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
	        }

	        if ($guardName) {
	            return collect($guardName);
	        }

	        return self::getConfigAuthGuards($class);
	    }

  protected static function getConfigAuthGuards(string $class): Collection
    {
        return collect(config('manage.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return null;
                }

                return config("manage.providers.{$guard['provider']}.model");
            })
            ->filter(function ($model) use ($class) {
                return $class === $model;
            })
            ->keys();
    }

    
    public static function getDefaultName($class): string
    {
        $default = config('manage.defaults.guard');

        $possible_guards = static::getNames($class);

        // return current-detected auth.defaults.guard if it matches one of those that have been checked
        if ($possible_guards->contains($default)) {
            return $default;
        }

        return $possible_guards->first() ?: $default;
    }
}