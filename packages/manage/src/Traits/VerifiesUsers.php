<?php

namespace Deyji\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Deyji\Manage\Facades\Manage as ManageFacade;

// To be jettisoned
trait VerifiesUser{

	public function getVerification(Request $request, $token){
		if($this->validateRequest($request)){
			return response()->json(["message"=>"Something Went Wrong"]);
		}

		return response(["message"=>"Success!"]);
	}

	protected function validateRequest(Request $request){
		$validator = Validator::make($request->all(), [
			"email"=>"required|email"
		]);

		return $validator->validated();
	}
}