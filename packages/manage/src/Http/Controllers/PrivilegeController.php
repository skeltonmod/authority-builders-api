<?php

namespace Deyji\Manage\Http\Controllers;

use App\Http\Controllers\Controller;
use Deyji\Manage\Models\Privilege\Permission;
use Illuminate\Http\Request;
use Laravel\Passport;


use Deyji\Manage\Models\Privilege\Role;
use Deyji\Manage\Models\Users;

class PrivilegeController extends Controller{
	// Create Role
	public function create_role(Request $request){
		$role =	Role::query()->create(["name"=>ucfirst($request['name']), 'guard_name'=>'api']);
		$role->save();
		return response()->json([
			"message"=>"Role added successfully",
			"role"=>$role->name,
			"guard_name"=>$role->guard_name
		]);
	}

	// Attach Role to user
	public function attach_role(Request $request){
		$user = Users::query()->find($request['id']);
		if($user->assignRole($request['new_role'])){
			return response()->json([
				"message"=>"Role attached successfully",
				"role"=>$user->roles
			]);
		}
	}

	// Create Permission
	public function create_permissions(Request $request){
		$permission = Permission::query()->create(['name'=>$request['name'], 'guard_name'=>'api']);
		$permission->save();
		return response()->json([
			"message"=>"Permission/s added successfully",
			"role"=>$permission->name,
			"guard_name"=>$permission->guard_name
		]);
	}

	// Attach Permission to a Role
	// Not to be confused with the private function call
	public function attach_permissions(Request $request){
		$role = Role::query()->find($request['id']);
		if($role->givePermissionTo($request['permission'])){
			return response()->json([
				"message"=>"Permission/s attached successfully",
				"role"=>$role->permissions
			]);
		}
	}

	// Update role
	public function update_role(Request $request){
		$role = Role::query()->find($request['id']);
		$role->name = $request['name'];

		// Clear the permissions first
		$role->permissions()->detach();

		// Check if we have appended permissions
		if($request['permissions'] != null && is_array($request['permissions'])){
			foreach($request['permissions'] as $perms){
				$role->givePermissionTo($perms);
			}
		}else{
			// Means we removed all the permissions
			$role->permissions()->detach();
		}

		if($role->save()){
			return response()->json([
				"message"=>"Role updated successfully",
				"role"=>$role->name
			]);
		}
	}

	// Update permission
	public function update_permission(Request $request){
		$permission = Permission::query()->find($request['id']);
		$permission->name = $request['name'];
		if($permission->save()){
			return response()->json([
				"message"=>"Permission updated successfully",
				"role"=>$permission->name
			]);
		}
	}

	// Get All roles
	public function get_all_roles(){
		$role = Role::all();
		return response($role);
	}


	public function middleware_test(){
		dd("called!");
	}

	public function get_permissions(){
		$perms = Permission::all();
		return response($perms);
	}

	public function get_role_permissions(Request $request){
		$role = Role::query()->find($request['id']);
		return response($role->permissions);
	}
}