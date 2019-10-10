<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('signed')->only('verify');
        // $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\AuthorizationException;
     */
    public function verify(Request $request)
    {

        if (! hash_equals((string) $request->id, (string) $request->user()->getKey())) {
            throw new AuthorizationException();
        }

        if (! hash_equals((string) $request->hash, sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user()->markEmailAsVerified()) {
            return apiResponse(200,null,'Email verified');
        }
    }
}
