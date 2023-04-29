<?php

namespace Deyji\Manage\Http\Middleware;

use Closure;
use Deyji\Manage\Models\Privilege\Role;
use Deyji\Manage\Models\Users;
use Exception;
use Illuminate\Support\Facades\Auth;


class RoleMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $guard = null)
    {
        $auth = Auth::user();
        $roles = is_array($role) ? $role : explode('|', $role);
        if(!Users::query()->find($auth->id)->hasAnyRole($roles)){
            // Bawal ang walay role
            throw new Exception("Role not permitted!");
        }

        return $next($request);
    }
}
