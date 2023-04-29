<?php

namespace Deyji\Manage\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Deyji\Manage\Models\Users;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Lcobucci\JWT\Token\Parser;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    use ThrottlesLogins;
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $maxAttempts = 2;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        $request->validate([
            "email"=>"required|email",
            "password"=>"required|string"
        ]);

        $credentials = $request->only('email', 'password');
        if(Auth::guard('web')->attempt($credentials)){

            $user = Users::query()->where('email', $request['email'])->first();
            $token = $user->createToken('Personal Access Token');
            // Infer the week
            $token->token->expires_at = Carbon::now()->addWeeks(1);

            return response()->json([
            'access_token' => $token->token,
            'token_type' => 'Bearer',
            'token'=>$token->accessToken,
            'expires_at' => Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString()
        ]);

        }else{
            return response()->json(['message' => 'Account not Found'], 401);
            $this->incrementLoginAttempts($request);
        }


    }

    public function logout(Request $request){
        // Delete the tokens
        return response([
            "message"=>"Logged out!",
            "count"=>Users::query()->find(Auth::id())->tokens()->delete()
        ]);
    }

}
