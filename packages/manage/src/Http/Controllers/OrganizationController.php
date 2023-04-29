<?php

namespace Deyji\Manage\Http\Controllers;

use Deyji\Manage\Models\Organizations;
use Deyji\Manage\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    //

    public function create_organization(Request $request){
        $organization = Organizations::create([
            'name'=> $request['name'],
            'guard_name'=>'api'
        ]);


    }

    public function attach_organization(Request $request){
        $user = Users::query()->find(Auth::id());
        $organization = Organizations::query()->find($request['organization_id']);
        $organization->users()->attach($user);
    }

    public function get_organizations(){
        return response(Organizations::all());
    }

    public function get_user_organization(Request $request){
        $user = Users::query()->find(Auth::id());
        $organization = Organizations::query()->find($user->organization[0]->pivot->organizations_id);
        return response($organization);
    }

    // public function get_user_organization(Request $request){
    //     $user = Users::query()->find($request['id']);
    //     $organization = Organizations::query()->find($user->organization[0]->pivot->organizations_id);
    //     return response($organization);
    // }

    public function update_organization(Request $request){
        $organization = Organizations::query()->find($request['id']);
        $organization->name = $request['name'];
        if($organization->save()){
            return response()->json([
                "message"=>"Organization updated successfully",
                "role"=>$organization->name
            ]);
        }
    }

}
