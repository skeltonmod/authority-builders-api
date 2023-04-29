<?php

namespace Deyji\Manage\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Deyji\Manage\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Deyji\Manage\Facades\UserVerification;
use Deyji\Manage\Mail\InviteEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    public function register(Request $request)
    {
        $request->validate([
            "name"=> "required|string",
            "email"=>"required|email",
            "password"=>"required|string",
            "business" => 'required|string',
            'phone_number' => 'required|string',
            'country' => 'required|string',
            'position' => 'required|string'
        ]);

        // Run once instead of using a trait
        // If not invited, send a verify link
        if(!$request->invited){
            $user = Users::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            event(new Registered($user));
            UserVerification::generate($user);
            UserVerification::send($user, "Verify Email!");
        }else{
            $user = Users::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'verified'=> 1,
                'verification_token'=> hash_hmac('sha256', Str::random(40), "123")
            ]);
        }

        $token = $user->createToken("Personal Access Token");
        $user->assignRole('User');
        return response()->json([
            'message' => 'Successfully created user!',
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString(),
            'role'=>$user->roles
        ]);

    }

    public function InviteRegister(Request $request){
        return Mail::to($request->email)->send(new InviteEmail($request->email));
    }
}
