<?php

use Illuminate\Http\Request;

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

    Route::group(['middleware' => 'auth:api'], function(){

        Route::group(['prefix' => 'classroom'], function(){
            Route::post('new', 'ClassroomController@create');
            Route::get('list', 'ClassroomController@list');
            Route::get('my-classroom', 'ClassroomController@myClassroom');
            Route::get('classroom-details/{slug}', 'ClassroomController@details');

            Route::group(['prefix' => 'invitation'], function(){
                Route::get('generate-public-invitation', 'InvitationController@generatePublicInvitation');
                Route::post('send-private-invitation', 'InvitationController@sendPrivateInvitation');
                Route::get('join', 'InvitationController@joinClassroom');
            });

            Route::group(['prefix' => '{slug}/assignment'], function(){
                Route::post('create-assignment', 'AssignmentController@create');
                Route::get('/view-assignment/{teachableId}', 'AssignmentController@viewAssignment');
                Route::post('/{teachableId}/upload-submission', 'AssignmentController@uploadSubmission');
                Route::get('/{teachableId}/list-submission/', 'AssignmentController@listSubmission');
            });

            Route::group(['prefix' => '{slug}/resource'], function(){
                Route::post('create-resource', 'ResourcesController@create');
            });
        });

        Route::group(['prefix' => 'account'], function(){
            Route::post('update-info', 'AccountController@updateInfo');
            Route::post('change-avatar', 'AccountController@changeAvatar');
            Route::get('profile', 'AccountController@profile');
            Route::post('change-password', 'AccountController@changePassword');
        });

        Route::group(['prefix' => 'discover'], function(){
            Route::get('by-classname', 'DiscoverController@byClassName');
            Route::get('by-tags', 'DiscoverController@byTags');
        });

        Route::group(['prefix' => 'tag'], function(){
            Route::get('get-all-tags', 'TagController@getAllTags');
            Route::get('autocomplete', 'TagController@autocomplete');
        });

        Route::get('logout', function(){
            $userTokens = auth('api')->user()->tokens;
            foreach ($userTokens as $token) {
                $token->revoke();
            }
            return apiResponse(200, null);
        });

    });
});
