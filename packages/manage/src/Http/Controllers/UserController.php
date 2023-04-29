<?php

namespace Deyji\Manage\Http\Controllers;

use Deyji\Manage\Models\Maps;
use Deyji\Manage\Models\Organizations;
use Deyji\Manage\Models\Privilege\Role;
use Deyji\Manage\Models\Users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpseclib3\Crypt\Hash;

class UserController extends Controller
{
    //
    public function get_all_users()
    {

        $users = Users::all();

        $response = null;
        foreach ($users as $user) {
            // Don't get your own account
            if (Auth::user()->id == $user->id) {
                continue;
            }

            $response[] = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "status" => "Active",
                "roles" => $user->roles,
                "organization" => isset($user->organization[0]) ? $user->organization[0]->name: null
            ];
        }

        return response($response);
    }

    // Update user from the UserList Front End
    public function update_user(Request $request)
    {
        // Sanity check
        if (Auth::user()->email == $request['email']) {
            throw new Exception("You can't edit your own account");
        }


        // find the user
        $user = Users::query()->where('email', $request['email'])->first();
        foreach (array_keys($request->all()) as $field) {

            if ($field == 'status' || $field == 'role'  || $field == 'organization' ) {
                continue;
            }

            $user->$field = $request[$field];
        }

        if ($request['organization'] != null) {
            // check if user has no organization, yet
            if (!isset($user->organization[0])) {
                $organization = Organizations::query()->find($request['organization']['id']);
                $organization->users()->attach($request['organization']['id']);
            } else {
                $user->organization()->updateExistingPivot(
                    $user->organization[0]->pivot->organizations_id,
                    ['organizations_id' => $request['organization']['id']]
                );
            }
        }


        if ($request['role'] != null) {
            $user
                ->roles()
                ->updateExistingPivot(
                    $user
                        ->roles[0]
                        ->pivot
                        ->role_id,
                    ['role_id' => $request['role']]
                );
        }



        if ($user->save()) {
            return response(['message' => "Profile Edited successfully!"]);
        } else {
            return response(['message' => "Fatal Mistake!"]);
        }
    }

    // Update the signed in user
    public function update_current_user(Request $request)
    {
        $request->validate([
            "name" => "string",
            "email" => "email",
        ]);
        $user = Users::query()->find(Auth::user()->id);

        // Get the fields dynamically
        foreach (array_keys($request->all()) as $field) {

            if ($field == 'password') {
                $user->$field = bcrypt($request[$field]);
            } else {
                $user->$field = $request[$field];
            }
        }

        if ($user->save()) {
            return response(['message' => "Profile Edited successfully!"]);
        } else {
            return response(['message' => "Fatal Mistake!"]);
        }
    }

    public function get_account_details()
    {
        $user = Users::query()->find(Auth::id());
        return response($user);
    }

    public function update_user_location(Request $request)
    {
        // check if I have setup my coords already
        $user = Auth::user();
        $map = Maps::query()->where('user_id', $user->id)->first();

        if ($map == null) {
            // We must make a new entry in the map model
            Maps::create([
                'latitude' => $request['latitude'],
                'longitude' => $request['longitude'],
                'canonical_name' => 'Some City',
                'status' => 1,
                'user_id' => $user->id
            ]);
        } else {
            if ($request['latitude'] != null && $request['longitude'] != null) {
                $map->latitude = $request['latitude'];
                $map->longitude = $request['longitude'];
            }
            if ($request['status'] != null) {
                $map->status = $request['status'];
            }
            $map->save();
            return response($map);
        }
    }

    public function get_user_location(Request $request)
    {
        // check if I have setup my coords already
        $user = Auth::user();
        $map = Maps::query()->where('user_id', $user->id)->first();

        return response($map == null ? null : $map);
    }
}
