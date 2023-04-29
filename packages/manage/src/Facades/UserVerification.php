<?php
namespace Deyji\Manage\Facades;

use Illuminate\Support\Facades\Facade;


class UserVerification extends Facade{

	protected static function getFacadeAccessor(){
		return "user.verification";
	}
}