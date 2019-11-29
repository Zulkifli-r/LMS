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
            Route::get('trashed', 'ClassroomController@trashed');
            Route::get('{slug}/list-students', 'ClassroomController@listStudents');
            Route::post('{slug}/remove-student', 'ClassroomController@removeStudent');
            Route::post('{slug}/update', 'ClassroomController@update');
            Route::get('{slug}/delete', 'ClassroomController@delete');
            Route::get('{slug}/hard-delete', 'ClassroomController@hardDelete');

            Route::group(['prefix' => 'invitation'], function(){
                Route::get('generate-public-invitation', 'InvitationController@generatePublicInvitation');
                Route::post('send-private-invitation', 'InvitationController@sendPrivateInvitation');
                Route::get('join', 'InvitationController@joinClassroom');
            });

            Route::group(['middleware'=>'classroom-resource'], function(){
                Route::group(['prefix' => '{slug}/assignment'], function(){
                    Route::post('create-assignment', 'AssignmentController@create')->middleware('classroom-teacher');
                    Route::get('list', 'AssignmentController@list');
                    Route::get('/view-assignment/{teachableId}', 'AssignmentController@viewAssignment');
                    Route::post('/{teachableId}/upload-submission', 'AssignmentController@uploadSubmission');
                    Route::get('/{teachableId}/list-submission/', 'AssignmentController@listSubmission');
                });

                Route::group(['prefix' => '{slug}/quiz'], function(){
                    Route::post('create-quiz', 'QuizController@create')->middleware('classroom-teacher');
                    Route::get('list', 'QuizController@list');
                    Route::get('trashed', 'QuizController@trashed')->middleware('classroom-teacher');

                    Route::get('{quiz}/details', 'QuizController@details');
                    Route::post('{quiz}/update', 'QuizController@update')->middleware('classroom-teacher');
                    Route::get('{quiz}/publish', 'QuizController@publish')->middleware('classroom-teacher');
                    Route::get('{quiz}/unpublish', 'QuizController@unpublish')->middleware('classroom-teacher');
                    Route::get('{quiz}/delete', 'QuizController@delete')->middleware('classroom-teacher');
                    Route::get('{quiz}/hard-delete', 'QuizController@hardDelete')->middleware('classroom-teacher');

                    // Question section
                    Route::post('{quiz}/create-question', 'QuizController@createQuestion')->middleware('classroom-teacher');
                    Route::post('{quiz}/update/{question}', 'QuizController@updateQuestion')->middleware('classroom-teacher');
                    Route::get('{quiz}/delete/{question}', 'QuizController@deleteQuestion')->middleware('classroom-teacher');
                    Route::get('{quiz}/force-delete/{question}', 'QuizController@forceDeleteQuestion')->middleware('classroom-teacher');

                    // Attempt
                    Route::post('{quiz}/attempt', 'QuizController@attempt')->middleware('classroom-student');
                    Route::post('{quiz}/update-attempt', 'QuizController@updateAttempt')->middleware('classroom-student');
                });

                Route::group(['prefix' => '{slug}/resource'], function(){
                    Route::post('create', 'ResourceController@create')->middleware('classroom-teacher');
                    Route::get('list', 'ResourceController@list');
                    Route::get('trashed', 'ResourceController@trashed')->middleware('classroom-teacher');

                    Route::get('{resource}/details', 'ResourceController@details');
                    Route::get('{resource}/delete', 'ResourceController@delete')->middleware('classroom-teacher');
                    Route::get('{resource}/hard-delete', 'ResourceController@hardDelete')->middleware('classroom-teacher');
                });

            });


        });

        Route::group(['prefix' => 'account'], function(){
            Route::post('update-info', 'AccountController@updateInfo');
            Route::post('change-avatar', 'AccountController@changeAvatar');
            Route::get('profile', 'AccountController@profile');
            Route::post('change-password', 'AccountController@changePassword');
        });

        Route::group(['prefix' => 'classes'], function(){
            Route::get('index', 'ClassesController@index');
            Route::get('detail-created', 'ClassesController@detailCreated');
            Route::get('detail-joined', 'ClassesController@detailJoined');
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
