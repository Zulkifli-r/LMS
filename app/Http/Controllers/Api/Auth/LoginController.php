<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\TooManyAttemptException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $validator = $this->validateLogin($request->all());
        if ($validator->fails()) {
            return apiResponse(
                400,null,$validator->errors()
            );
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user = $request->user();

            return $this->loggingIn($user);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);

        // return apiResponse(400,null,'Incorrect credentials provided');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\User::where('email',$request->email)->first();
        if ($user) {
            if ($user->password != \Hash::make($request->password)) {
                return apiResponse(400,null,'Password missmatch');
            }
        }
        else{
            return apiResponse(404,null,'User not found');
        }
    }

    protected function validateLogin(array $data)
    {
        return Validator::make($data,
            [
                $this->username() => 'email|required|string',
                'password' => 'required|string',
                'remember_me' => 'boolean'
            ]
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw new TooManyAttemptException(
            $this->username().' '.Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60) ] )
            );
    }

    public function handleProviderCallback(Request $request)
    {
        $providerInfo = Socialite::driver($request->provider)->userFromToken($request->token);

        $user = \App\User::where('email', $providerInfo->email)->first();

        if (!$user) {
            // register user
            $user = new \App\User();
            $user->name = $providerInfo->name;
            $user->email = $providerInfo->email;
            $user->email_verified_at = \Carbon\Carbon::now();
            $user->password = Hash::make(Str::random(20));
            $user->is_provider = true;

            $user->assignRole(['user']);
            $user->addMediaFromUrl($providerInfo->avatar)->toMediaCollection('avatar');

            $user->save();

            // fire registered user event
            event(new UserRegistered($user));
        }

        return $this->loggingIn($user);

    }

    private function loggingIn($user)
    {
        Passport::tokensExpireIn(now()->addMinutes(1));
        Passport::refreshTokensExpireIn(now()->addMinutes(1));

        $token = $user->createToken('User personal access token');
        // $token->token->expires_at = Carbon::now()->addMinutes(5);

        return apiResponse(200, [
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->email_verified_at ? true:false,
            'avatar' => count($user->getMedia('avatar')) ? asset($user->getMedia('avatar')->first()->getUrl()) : null,
            'roles' => $user->role,
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString()
        ] );
    }

}
