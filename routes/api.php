<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::domain('localhost:3000/verify', function(){ return null; } )->name('email.verification.verify');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group( ['namespace' => 'Api'], function(){

    // Auth controller
    Route::group( ['namespace' => 'Auth'], function(){
        Route::post('login', 'LoginController@login');

        Route::post('register', 'RegisterController@register');
        Route::post('provider-callback', 'LoginController@handleProviderCallback');
        Route::get('verify-email','VerificationController@verify');
        Route::post('forgot-password', 'ForgotPasswordController@sendResetLinkEmail');
        Route::get('reset-password', 'ResetPasswordController@reset');

    });

    Route::group( ['middleware' => 'auth:api'], function(){

        Route::get('logout', function(){
            $userTokens = auth('api')->user()->tokens;
            foreach ($userTokens as $key => $token) {
                $token->revoke();
            }

            return apiResponse(200, null);
        });

        Route::group(['prefix' => 'classroom'], function(){
            Route::post('new', 'ClassroomController@create');
            Route::get('list', 'ClassroomController@list');
            Route::get('my-classroom', 'ClassroomController@myClassroom');

                Route::group(['prefix' => 'invitation'], function(){
                    Route::get('generate-public-invitation', 'InvitationController@generatePublicInvitation');
                    Route::post('send-private-invitation', 'InvitationController@sendPrivateInvitation');
                    Route::get('join', 'InvitationController@joinClassroom');
                });

        });

        Route::group(['prefix' => 'account'], function(){
            Route::post('update-info', 'AccountController@updateInfo');
            Route::post('change-avatar', 'AccountController@changeAvatar');
        });

    });
});
