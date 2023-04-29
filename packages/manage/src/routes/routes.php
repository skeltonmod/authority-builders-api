<?php

use Deyji\Manage\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Deyji\Manage\Http\Controllers\Auth\VerificationController;
use Deyji\Manage\Http\Controllers\Auth\RegisterController;
use Deyji\Manage\Http\Controllers\Auth\AuthenticationController;
use Deyji\Manage\Http\Controllers\Auth\SocialController;
use Deyji\Manage\Http\Controllers\ConfigController;
use Deyji\Manage\Http\Controllers\PrivilegeController;
use Deyji\Manage\Http\Controllers\UserController;
use Deyji\Manage\Http\Controllers\OrganizationController;
use Deyji\Manage\Http\Controllers\TimeZoneController;

Route::group(["middleware" => ["token"]], function () {
	Route::get('email-verification/check/{token}', [VerificationController::class, 'verifyEmail'])
		->name('email-verification.check');

	Route::get('/app/{vue?}', function (Request $request) {
		return view('app');
	})->where('vue', '[\/\w\.-]*')->name('app');

});



Route::group([
	"middleware" => ["api", "token"],
	"prefix"     => "api"
], function () {
	Route::post('/register', [RegisterController::class, 'register']);
	Route::post('/login', [LoginController::class, 'login']);

	Route::post('/socialauth/{provider}', [SocialController::class, 'SocialLogin']);

	Route::post('/inviteuser', [RegisterController::class, 'InviteRegister']);
});

Route::group([
	"middleware" => ["api", "role:Admin", "auth:api"],
	"prefix"     => "api"
], function () {
	Route::get('/middlewaretest', [PrivilegeController::class, 'middleware_test']);
	Route::post('/createpermissions', [PrivilegeController::class, 'create_permissions']);
	Route::post('/updatepermission', [PrivilegeController::class, 'update_permission']);
	Route::post('/attachpermissions', [PrivilegeController::class, 'attach_permissions']);
	Route::post('/updateuser', [UserController::class, 'update_user']);
	Route::post('/updaterole', [PrivilegeController::class, 'update_role']);

	// Organization
	Route::post('/createorganization', [OrganizationController::class, 'create_organization']);

	// Update Organization (name)
	Route::post('/updateorganization', [OrganizationController::class, 'update_organization']);

	// Attach Organization
	Route::post('/attachorganization', [OrganizationController::class, 'attach_organization']);
	// GET Organizations
	Route::get('/getorganizations', [OrganizationController::class, 'get_organizations']);
	// Get Organizaztion of the user
	Route::get('/getuserogranization', [OrganizationController::class, 'get_user_organization']);

	// Save Config
	Route::post("/saveconfig", [ConfigController::class, 'save_config'])->middleware('log:update');
});

Route::group([
	"middleware" => ["api", "auth:api"],
	"prefix"     => "api"
], function () {
	
	// For JWT authentication/page-guard to properly work
	Route::post('/checkauth', [AuthenticationController::class, 'check']);

	Route::post('/attachrole', [PrivilegeController::class, 'attach_role'])->middleware('log:create');
	Route::post('/createrole', [PrivilegeController::class, 'create_role'])->middleware('log:create');

	// Read from API
	Route::get('/getroles', [PrivilegeController::class, 'get_all_roles'])->middleware('log:read');;
	Route::post('/getroleperms', [PrivilegeController::class, 'get_role_permissions'])->middleware('log:read');
	Route::get('/getpermissions', [PrivilegeController::class, 'get_permissions'])->middleware('log:read');
	Route::get('/getusers', [UserController::class, 'get_all_users'])->middleware('log:read');;
	Route::get('/getaccount', [UserController::class, 'get_account_details'])->middleware('log:read');
	Route::get('/getlocation', [UserController::class, 'get_user_location'])->middleware('log:read');

	// Update from API
	Route::post('/updateaccount', [UserController::class, 'update_current_user'])->middleware('log:update');
	Route::post('/updatelocation', [UserController::class, 'update_user_location'])->middleware('log:update');

	Route::post('/logout', [LoginController::class, 'logout'])->middleware('log:logout');

	Route::get("/getimezone", [TimeZoneController::class, 'get_timezone']);


});

