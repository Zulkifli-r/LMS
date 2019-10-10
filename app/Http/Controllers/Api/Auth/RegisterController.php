<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->generateAvatar();
        $user->assignRole(['user']);

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails()){
            return apiResponse(400, null, $validator->errors());
        }

        $user = $this->create($request->all());

        $token = $user->createToken('User personal access token');

        event(new UserRegistered($user));
        return apiResponse(200, [
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->email_verified_at ? true:false,
            'avatar' => count($user->getMedia('avatar')) ? asset($user->getMedia('avatar')->first()->getUrl()) : null,
            'roles' => $user->role,
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => \Carbon\Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString()
        ] );

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

}
