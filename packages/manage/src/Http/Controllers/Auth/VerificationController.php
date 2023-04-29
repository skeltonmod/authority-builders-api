<?php

namespace Deyji\Manage\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;
use Deyji\Manage\Facades\UserVerification;
class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */
    // Verify Email once the user clicks the link
    public function verifyEmail(Request $request, $token){
        dd(UserVerification::check($request['email'], $token, "users"));
    }

}
