<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['domain' => env('FE_VERIFY_ROUTE','localhost:3000/')], function()
{
    Route::get('/verify', function()
    { return null; })->name('fe-verify-route');

    Route::get('/invitation/join', function()
    { return null; })->name('fe-invitation-route');

});