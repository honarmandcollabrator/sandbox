<?php
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

Route::group([
    'prefix' => 'auth', 'as' => 'auth.'
], function () {
    Route::post('register', 'Auth\RegisterController@create')->name('register');
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('me', 'AuthController@me')->name('me');
    Route::post('refresh', 'AuthController@refresh')->name('refresh');
    Route::post('logout', 'AuthController@logout')->name('logout');
    Route::post('forgot-password-email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('forgot.password.email');
    Route::post('forgot-password-reset', 'Auth\ResetPasswordController@reset')->name('forgot.password.reset');
});

Route::group([
    'prefix' => 'verification', 'as' => 'verification.'
], function () {
    Route::post('email/verify/{id}', 'Auth\VerificationController@verify')->name('verify');
    Route::post('email/resend', 'Auth\VerificationController@resend')->name('resend');
});


Route::group([
    'middleware' => ['auth','verified']
], function () {

});
