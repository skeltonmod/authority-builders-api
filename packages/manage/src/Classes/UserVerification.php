<?php

namespace Deyji\Manage\Classes;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Schema\Builder;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Deyji\Manage\Mail\VerificationTokenGenerated;
use Exception;
use Illuminate\Support\Facades\Mail;

class UserVerification{

	public function generate(AuthenticatableContract $user){

		return $this->saveToken($user, $this->generateToken());

	}

	public function check($email, $token, $userTable){
		$user = $this->getUserByEmail($email, $userTable);

		unset($user->{"password"});


		$this->isVerified($user);
		$this->verifyToken($user->verification_token, $token);

		$this->wasVerified($user);

		return $user;
	}

	protected function generateToken(){
		return hash_hmac('sha256', Str::random(40), "123");
	}

	protected function saveToken(AuthenticatableContract $user, $token){
		$user->verified = false;
		$user->verification_token = $token;

		return $user->save();
	}

	// Protected Function

	protected function getUserByEmail($email, $table){
		$user = DB::table($table)
				->where("email", $email)
				->first();
		if($user === null){
			throw new Exception("No User Found!");
		}

		$user->table = $table;

		return $user;
	}

	public function send(AuthenticatableContract $user, $subject = null, $from = null, $name = null){
		$this->emailVerificationLink($user, $subject, $from, $name);
	}

	protected function emailVerificationLink(AuthenticatableContract $user, $subject = null, $from = null, $name = null){
		return Mail::to($user)->send(new VerificationTokenGenerated($user, $subject, $from, $name));
	}

	protected function isCompliant()
    {
        $user = config('auth.providers.users.model', Deyji\Manage\Models\Users::class);

        return $this->schema->hasColumns((new $user())->getTable(), ['verified', 'verification_token'])? true : false;
    }

    protected function isVerified($user)
    {
        if ($user->verified == true) {
            throw new Exception("User is Already verified");
        }
    }

    protected function verifyToken($storedToken, $requestToken)
    {
        if ($storedToken != $requestToken) {
            throw new Exception("Token Mismatch");
        }
    }

    protected function wasVerified($user)
    {
        $user->verification_token = null;

        $user->verified = true;

        $user->verified_at = now();

        $this->updateUser($user);

        // event(new UserVerified($user));
    }

    protected function updateUser($user){
    	DB::table($user->table)
    	->where("email", $user->email)
    	->update([
                'verified' => $user->verified,
                'email_verified_at' => $user->verified_at,
    	]);
    }
}
