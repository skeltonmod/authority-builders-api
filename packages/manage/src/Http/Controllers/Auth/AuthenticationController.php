<?php

namespace Deyji\Manage\Http\Controllers\Auth;

use Deyji\Manage\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    //

    public function check(Request $request){
        if(Users::query()->find(Auth::id())->tokens()){
            return response([true]);
        }
    }
}
