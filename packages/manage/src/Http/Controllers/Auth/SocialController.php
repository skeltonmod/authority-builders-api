<?php

namespace Deyji\Manage\Http\Controllers\Auth;

use Carbon\Carbon;
use Deyji\Manage\Models\Users;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{

    public function SocialLogin($provider)
    {
        $users = Socialite::driver($provider)->stateless()->user();

        $user = Users::query()->where('email', $users->getEmail())->first();

        if ($user) {
            $token = $user->createToken('Personal Access Token');
            // Infer the week
            $token->token->expires_at = Carbon::now()->addWeeks(1);

            Auth::login($user);

            return response()->json([
                'access_token' => $token->token,
                'token_type' => 'Bearer',
                'token' => $token->accessToken,
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString()
            ]);
        } else {
            $user = Users::create([
                'name' => $users->getName(),
                'email' => $users->getEmail(),
                'socialite_id' => $users->getId(),
                'password'=>Hash::make($users->getId())
            ]);

            $token = $user->createToken("Personal Access Token");
            $token->token->expires_at = Carbon::now()->addWeeks(1);

            $user->assignRole('User');
            Auth::login($user);
            return response()->json([
                'message' => 'Successfully created user!',
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'token' => $token->accessToken,
                'expires_at' => Carbon::parse(
                    $token->token->expires_at
                )->toDateTimeString(),
                'role' => $user->roles
            ]);
        }
    }
}
